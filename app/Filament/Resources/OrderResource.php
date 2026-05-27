<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\OrderStatusService;
use App\Services\BiteshipService;
use App\Models\Store;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Umum')
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->label('No. Pesanan')
                                    ->disabled(),
                                Forms\Components\TextInput::make('created_at')
                                    ->label('Tanggal Pesan')
                                    ->disabled()
                                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d M Y H:i')),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->disabled(),
                            ]),
                        Forms\Components\Section::make('Informasi User')
                            ->schema([
                                Forms\Components\TextInput::make('user.email')
                                    ->label('Email User')
                                    ->formatStateUsing(fn($record, $state) => $record?->user?->email ?? '-')
                                    ->disabled(),
                                Forms\Components\TextInput::make('user.name')
                                    ->label('Nama User')
                                    ->formatStateUsing(fn($record, $state) => $record?->user?->name ?? '-')
                                    ->disabled(),
                            ]),
                        Forms\Components\Section::make('Penerima')
                            ->schema([
                                Forms\Components\TextInput::make('recipient_name')
                                    ->label('Nama Penerima')
                                    ->disabled(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->disabled(),
                                Forms\Components\Textarea::make('shipping_address')
                                    ->disabled()
                                    ->visible(fn($record) => $record && $record->getOrderType() !== 'digital'),
                                Forms\Components\Textarea::make('noted')
                                    ->disabled(),
                            ]),


                        Forms\Components\Section::make('Status Order')
                            ->schema([
                                Forms\Components\TextInput::make('payment_gateway_transaction_id')
                                    ->label('Snap Token Midtrans')
                                    ->disabled()
                                    ->visible(
                                        fn($record) =>
                                        $record?->payment_gateway_transaction_id != null
                                    ),
                                Forms\Components\FileUpload::make('payment_proof')
                                    ->label('Bukti Pembayaran')
                                    ->image()
                                    ->disk('public')
                                    ->directory('payment-proofs')
                                    ->visible(
                                        fn($record) =>
                                        $record?->payment_gateway_transaction_id == null
                                    )
                                    ->disabled(),
                                Forms\Components\Select::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        OrderStatusService::PAYMENT_UNPAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_UNPAID),
                                        OrderStatusService::PAYMENT_PAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_PAID),
                                    ])
                                    ->required()
                                    ->live()
                                    ->disabled(
                                        fn($record) =>
                                        $record?->snap_token != null
                                    ),
                                Forms\Components\Toggle::make('is_approved')
                                    ->label('Pesanan Disetujui')
                                    ->helperText('Pesanan harus disetujui sebelum dapat diproses pengiriman')
                                    ->visible(
                                        fn(Forms\Get $get): bool =>
                                        $get('payment_status') === OrderStatusService::PAYMENT_PAID
                                    ),
                                Forms\Components\Placeholder::make('computed_status')
                                    ->label('Status Pesanan')
                                    ->content(function (Forms\Get $get, $record) {
                                        if (!$record)
                                            return 'Pesanan Baru';

                                        $paymentStatus = $get('payment_status') ?? $record->payment_status;
                                        $isApproved = $get('is_approved') ?? $record->is_approved;
                                        $shippingStatus = $record->shipping_status;

                                        $status = OrderStatusService::getOrderStatus($paymentStatus, $isApproved, $shippingStatus);
                                        return OrderStatusService::getOrderStatusLabel($status);
                                    }),
                                Forms\Components\TextInput::make('shipping_tracking_number')
                                    ->label('Nomor Resi')
                                    ->visible(
                                        fn(Forms\Get $get, $record): bool =>
                                        $record && $record->getOrderType() !== 'digital' &&
                                        $get('payment_status') === OrderStatusService::PAYMENT_PAID &&
                                        $get('is_approved') === true &&
                                        $record && $record->shipping_order_id
                                    )
                                    ->disabled(),
                                Forms\Components\TextInput::make('shipping_order_id')
                                    ->label('Shipping Order ID')
                                    ->disabled()
                                    ->helperText('ID order shipping dari Biteship')
                                    ->visible(fn($record) => $record && $record->getOrderType() !== 'digital'),
                                Forms\Components\Select::make('shipping_status')
                                    ->label('Status Pengiriman')
                                    ->options([
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'allocated' => 'Allocated',
                                        'picking_up' => 'Picking Up',
                                        'picked_up' => 'Picked Up',
                                        'dropping_off' => 'Dropping Off',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                        'returned' => 'Returned',
                                    ])
                                    ->disabled()
                                    ->visible(fn($record) => $record && $record->shipping_order_id && $record->getOrderType() !== 'digital')
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Detail Harga')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp'),
                                Forms\Components\TextInput::make('shipping_cost')
                                    ->label('Biaya Pengiriman')
                                    ->disabled()
                                    ->prefix('Rp')
                                    ->visible(fn($record) => $record && $record->getOrderType() !== 'digital'),
                                Forms\Components\TextInput::make('voucher_code')
                                    ->label('Kode Voucher')
                                    ->disabled()
                                    ->placeholder('Tidak ada voucher'),
                                Forms\Components\TextInput::make('voucher_discount')
                                    ->label('Diskon Voucher')
                                    ->disabled()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp'),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Pembayaran')
                                    ->disabled()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp'),
                            ]),
                        Forms\Components\Section::make('Pengiriman')
                            ->visible(fn($record) => $record && $record->getOrderType() !== 'digital')
                            ->schema([

                                Forms\Components\TextInput::make('shipping_provider')
                                    ->disabled(),
                                Forms\Components\TextInput::make('shipping_area_id')
                                    ->disabled(),
                                Forms\Components\TextInput::make('shipping_area_name')
                                    ->disabled(),
                                Forms\Components\TextInput::make('shipping_method_detail.courier_code')
                                    ->label('Kode Kurir')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier_code'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.courier_name')
                                    ->label('Nama Kurir')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier_name'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.courier_service_name')
                                    ->label('Layanan')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier_service_name'] ?? $data['service'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.courier_service_code')
                                    ->label('Service Code')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier_service_code'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.duration')
                                    ->label('Estimasi')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['duration'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.service_type')
                                    ->label('Tipe Service')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return ucfirst($data['service_type'] ?? '-');
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.shipping_type')
                                    ->label('Tipe Pengiriman')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return ucfirst($data['shipping_type'] ?? '-');
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.price')
                                    ->label('Harga')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return 'Rp ' . number_format($data['price'] ?? 0, 0, ',', '.');
                                    }),
                                Forms\Components\TextInput::make('shipping_method_detail.description')
                                    ->label('Deskripsi')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['description'] ?? '-';
                                    }),
                                Forms\Components\Toggle::make('shipping_method_detail.available_for_cash_on_delivery')
                                    ->label('COD Available')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return false;
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return (bool) ($data['available_for_cash_on_delivery'] ?? false);
                                    }),
                                Forms\Components\Toggle::make('shipping_method_detail.available_for_insurance')
                                    ->label('Insurance Available')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return false;
                                        $data = $record->shipping_method_detail;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return (bool) ($data['available_for_insurance'] ?? false);
                                    }),


                            ]),
                        Forms\Components\Section::make('Data Biteship Order')
                            ->schema([
                                Forms\Components\TextInput::make('shipping_order_data.id')
                                    ->label('Biteship Order ID')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['id'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.courier.waybill_id')
                                    ->label('Waybill ID')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier']['waybill_id'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.courier.tracking_id')
                                    ->label('Tracking ID')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier']['tracking_id'] ?? '-';
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.status')
                                    ->label('Status Biteship')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return ucfirst($data['status'] ?? '-');
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.courier.company')
                                    ->label('Company')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return strtoupper($data['courier']['company'] ?? '-');
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.courier.type')
                                    ->label('Service Type')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return strtoupper($data['courier']['type'] ?? '-');
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.price')
                                    ->label('Biaya Biteship')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return 'Rp ' . number_format($data['price'] ?? 0, 0, ',', '.');
                                    }),
                                Forms\Components\TextInput::make('shipping_order_data.courier.link')
                                    ->label('Tracking URL')
                                    ->disabled()
                                    ->formatStateUsing(function ($state, $record) {
                                        if (!$record) return '-';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        return $data['courier']['link'] ?? '-';
                                    })
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('biteship_tracking_link')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) return 'Belum tersedia';
                                        $data = $record->shipping_order_data;
                                        if (is_string($data))
                                            $data = json_decode($data, true);
                                        $link = $data['courier']['link'] ?? null;

                                        if ($link) {
                                            return new \Illuminate\Support\HtmlString(
                                                '<a href="' . $link . '" target="_blank" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    Buka Tracking Biteship
                                                </a>'
                                            );
                                        }

                                        return 'Belum tersedia';
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn($record) => $record && $record->shipping_order_data)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Penerima')
                    ->searchable(),
                Tables\Columns\TextColumn::make('voucher_code')
                    ->label('Voucher')
                    ->badge()
                    ->color('success')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->label('Total')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_type')
                    ->label('Tipe Pesanan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'digital' => 'info',
                        'physical' => 'warning',
                        'mixed' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'digital' => 'Pesanan Digital',
                        'physical' => 'Perlu Pengiriman',
                        'mixed' => 'Mixed',
                        default => 'Unknown',
                    })
                    ->state(fn($record) => $record->getOrderType())
                    ->sortable(query: function ($query, $direction) {
                        // Custom sorting based on order type
                        return $query->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                            ->select('orders.*')
                            ->groupBy('orders.id')
                            ->orderBy('products.is_product_digital', $direction);
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        OrderStatusService::PAYMENT_UNPAID => 'danger',
                        OrderStatusService::PAYMENT_PAID => 'success',
                    })
                    ->formatStateUsing(fn($state) => OrderStatusService::getPaymentStatusLabel($state)),
                Tables\Columns\TextColumn::make('computed_status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn($record): string =>
                        OrderStatusService::getOrderStatusColor(
                            $record->status,
                            $record->is_approved,
                            $record->shipping_status
                        )
                    )
                    ->state(
                        fn($record): string =>
                        OrderStatusService::getOrderStatusLabel(
                            OrderStatusService::getOrderStatus(
                                $record->payment_status,
                                $record->is_approved,
                                $record->shipping_status
                            )
                        )
                    ),
                Tables\Columns\IconColumn::make('payment_gateway_transaction_id')
                    ->label('Payment Gateway')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('shipping_method_detail')
                    ->label("Kurir")
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state)
                            return '-';

                        $data = $record->shipping_method_detail;
                        // Handle case where data is still JSON string
                        if (is_string($data)) {
                            $data = json_decode($data, true);
                        }

                        if (!is_array($data))
                            return '-';

                        $courierName = $data['courier_name'] ?? '-';
                        $serviceName = $data['courier_service_name'] ?? $data['service'] ?? '';
                        return $serviceName ? "{$courierName} - {$serviceName}" : $courierName;
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('shipping_tracking_number')
                    ->label('Resi')
                    ->searchable()
                    ->placeholder('-')
                    ->visible(fn() => in_array(request()->query('activeTab'), ['transit', 'delivered'])),

                Tables\Columns\TextColumn::make('shipping_status')
                    ->label('Status Pengiriman')
                    ->badge()
                    ->color(function (?string $state): string {
                        $simplified = \App\Services\ShippingStatusService::getSimplifiedStatus($state);
                        return match ($simplified) {
                            'pending' => 'warning',
                            'preparing' => 'info',
                            'pickup' => 'primary',
                            'transit' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            'returned' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(
                        fn(?string $state): string =>
                        \App\Services\ShippingStatusService::getShippingStatusLabel($state)
                    )
                    ->visible(fn() => in_array(request()->query('activeTab'), ['transit', 'delivered'])),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Action::make('preview_invoice')
                        ->label('Preview Invoice')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('info')
                        ->url(fn(Order $record) => url("invoice/{$record->id}/preview"))
                        ->openUrlInNewTab(),

                    // Step 1: Approve Payment (Menunggu Konfirmasi -> Diproses)
                    Action::make('approve_order')
                        ->label('Proses Pesanan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(
                            fn(Order $record): bool =>
                            $record->status === 'awaiting_confirmation'
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Proses Pesanan')
                        ->modalDescription('Apakah Anda yakin ingin memproses pesanan ini?')
                        ->action(function (Order $record) {
                            $record->update([
                                'is_approved' => true,
                                'shipping_status' => 'confirmed',
                                'status' => 'processing'
                            ]);

                            Notification::make()
                                ->title('Berhasil!')
                                ->body('Pesanan telah diproses.')
                                ->success()
                                ->send();
                        }),

                    // Step 2: Create Shipping Order (Diproses -> Dalam Pengiriman)
                    Action::make('create_shipping_order')
                        ->label('Kirim Pesanan')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->visible(
                            fn(Order $record): bool =>
                            $record->status === 'processing' &&
                            !$record->shipping_order_id &&
                            $record->getOrderType() !== 'digital' // Hide for digital orders
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Kirim Pesanan')
                        ->modalDescription('Apakah Anda yakin ingin mengirim pesanan ini? Order akan dibuat di Biteship dan status akan berubah menjadi Dikirim.')
                        ->action(function (Order $record) {
                            try {
                                static::createShippingOrder($record);

                                // Update status to shipped after successful order creation
                                $record->update([
                                    'shipping_status' => 'dropping_off',
                                    'status' => 'shipped'
                                ]);

                                Notification::make()
                                    ->title('Berhasil!')
                                    ->body('Pesanan berhasil dikirim dan order telah dibuat di Biteship.')
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal!')
                                    ->body('Gagal mengirim pesanan: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Complete Digital Order (Diproses -> Selesai untuk pesanan digital)
                    Action::make('complete_digital_order')
                        ->label('Selesaikan Pesanan')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(
                            fn(Order $record): bool =>
                            $record->status === 'processing' &&
                            $record->getOrderType() === 'digital'
                        )
                        ->form([
                            Forms\Components\Textarea::make('order_info')
                                ->label('Informasi Pesanan')
                                ->placeholder('Masukkan detail produk digital (link download, license key, instruksi akses, dll)')
                                ->required()
                                ->rows(5)
                                ->helperText('Informasi ini akan dikirimkan ke customer melalui WhatsApp'),
                        ])
                        ->action(function (Order $record, array $data) {
                            // Update order status to completed
                            $record->update([
                                'status' => 'completed',
                                'shipping_status' => 'delivered',
                                'noted' => ($record->noted ? $record->noted . "\n\n" : '') . "Info Pesanan Digital:\n" . $data['order_info']
                            ]);

                            // Format WhatsApp message
                            $phone = preg_replace('/[^0-9]/', '', $record->phone);
                            if (substr($phone, 0, 1) === '0') {
                                $phone = '62' . substr($phone, 1);
                            }

                            $message = "Halo {$record->recipient_name},\n\n";
                            $message .= "Pesanan digital Anda (#{$record->order_number}) telah selesai diproses!\n\n";
                            $message .= "*Detail Pesanan:*\n";

                            foreach ($record->items as $item) {
                                $message .= "• {$item->product_name} (x{$item->quantity})\n";
                            }

                            $message .= "\n*Informasi Akses:*\n";
                            $message .= $data['order_info'];
                            $message .= "\n\nTerima kasih telah berbelanja! 🎉";

                            $waUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

                            Notification::make()
                                ->title('Berhasil!')
                                ->body('Pesanan digital telah diselesaikan.')
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('open_whatsapp')
                                        ->label('Kirim ke WhatsApp')
                                        ->url($waUrl)
                                        ->openUrlInNewTab()
                                        ->button()
                                        ->color('success')
                                ])
                                ->persistent()
                                ->send();
                        }),

                    // Step 3: Update Shipping Status (realtime update)
                    Action::make('update_shipping_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(
                            fn(Order $record): bool =>
                            (bool) $record->shipping_order_id &&
                            !in_array($record->status, ['completed', 'cancelled'])
                        )
                        ->action(function (Order $record) {
                            try {
                                static::updateShippingStatus($record);

                                Notification::make()
                                    ->title('Berhasil!')
                                    ->body('Status pengiriman berhasil diupdate.')
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal!')
                                    ->body('Gagal update status: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Cancel Order
                    Action::make('cancel_order')
                        ->label('Batalkan Pesanan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(
                            fn(Order $record): bool =>
                            !in_array($record->status, ['completed', 'cancelled', 'shipped'])
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Pesanan')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan pesanan ini?')
                        ->action(function (Order $record) {
                            $record->update([
                                'shipping_status' => 'cancelled',
                                'status' => 'cancelled'
                            ]);

                            Notification::make()
                                ->title('Berhasil!')
                                ->body('Pesanan telah dibatalkan.')
                                ->success()
                                ->send();
                        }),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function createShippingOrder(Order $order): void
    {
        $store = Store::first();
        if (!$store || !$store->shipping_api_key) {
            throw new \Exception('Store atau API key shipping tidak ditemukan');
        }

        $shipping = new BiteshipService($store->shipping_api_key);

        // Get shipping method details
        $shippingMethod = $order->shipping_method_detail;
        if (!$shippingMethod) {
            throw new \Exception('Detail metode pengiriman tidak ditemukan');
        }

        // Handle case where data is still JSON string (legacy data)
        if (is_string($shippingMethod)) {
            $shippingMethod = json_decode($shippingMethod, true);
            if (!$shippingMethod) {
                throw new \Exception('Format detail metode pengiriman tidak valid');
            }
        }

        // Debug shipping method data
        \Log::info('Shipping method debug', [
            'order_id' => $order->id,
            'shipping_method_raw' => $shippingMethod,
            'courier_code' => $shippingMethod['courier_code'] ?? 'missing',
            'company' => $shippingMethod['company'] ?? 'missing',
            'courier_service_code' => $shippingMethod['courier_service_code'] ?? 'missing',
            'type' => $shippingMethod['type'] ?? 'missing',
        ]);

        // Extract courier company with robust fallback
        $courierCompany = $shippingMethod['company'] ??
            $shippingMethod['courier_code'] ??
            $shippingMethod['courier_name'] ??
            'jne';

        // Convert courier name to lowercase code if needed
        if (strlen($courierCompany) > 3) {
            $courierCompany = strtolower($courierCompany);
            if (strpos($courierCompany, 'jne') !== false)
                $courierCompany = 'jne';
            elseif (strpos($courierCompany, 'sicepat') !== false)
                $courierCompany = 'sicepat';
            elseif (strpos($courierCompany, 'j&t') !== false || strpos($courierCompany, 'jnt') !== false)
                $courierCompany = 'jnt';
            elseif (strpos($courierCompany, 'pos') !== false)
                $courierCompany = 'pos';
            else
                $courierCompany = 'jne'; // fallback
        }

        \Log::info('Courier mapping result', [
            'order_id' => $order->id,
            'final_courier_company' => $courierCompany,
            'courier_type' => static::mapCourierServiceCode($shippingMethod),
        ]);

        // Validate required data
        if (!$store->shipping_area_id) {
            throw new \Exception('Store shipping area ID tidak ditemukan. Pastikan store sudah dikonfigurasi dengan area pengiriman.');
        }

        if (!$order->shipping_area_id) {
            throw new \Exception('Order shipping area ID tidak ditemukan. Pastikan area tujuan sudah dipilih saat checkout.');
        }



        // Prepare items for Biteship API
        $items = $order->items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'description' => $item->variant_name ?? 'Product item',
                'value' => $item->price,
                'quantity' => $item->quantity,
                'weight' => 100, // Default weight if not specified
                'height' => 10,
                'length' => 10,
                'width' => 10,
            ];
        })->toArray();

        // Prepare order data for Biteship
        $orderData = [
            'reference_id' => $order->order_number,
            'shipper_name' => $store->name,
            'shipper_phone' => $store->whatsapp ?? '08123456789',
            'shipper_email' => $store->email_notification ?? '',
            'shipper_organization' => $store->name,
            'origin_address' => $store->address ?? 'Store Address',
            'origin_area_id' => $store->shipping_area_id,
            'origin_note' => 'Pengambilan paket dari toko',
            'destination_name' => $order->recipient_name,
            'destination_phone' => $order->phone,
            'destination_email' => $order->user->email ?? '',
            'destination_address' => $order->shipping_address,
            'destination_area_id' => $order->shipping_area_id,
            'destination_note' => $order->noted ?? '',
            'courier_company' => $courierCompany,
            'courier_type' => static::mapCourierServiceCode($shippingMethod),
            'delivery_type' => 'now', // Use 'now' as default, pickup might not be supported by all couriers
            'order_note' => $order->noted ?? 'Order dari ' . $store->name,
            'items' => $items,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'store_name' => $store->name
            ]
        ];

        // Log final order data before sending to API
        \Log::info('Final order data for Biteship API', [
            'order_id' => $order->id,
            'courier_company' => $orderData['courier_company'],
            'courier_type' => $orderData['courier_type'],
            'delivery_type' => $orderData['delivery_type'],
            'origin_area_id' => $orderData['origin_area_id'],
            'destination_area_id' => $orderData['destination_area_id'],
        ]);

        try {
            $shippingResult = $shipping->createOrder($orderData);

            // Update order with shipping information
            $order->update([
                'shipping_order_id' => $shippingResult['id'] ?? null,
                'shipping_order_data' => json_encode($shippingResult),
                'shipping_status' => $shippingResult['status'] ?? 'pending',
                'shipping_tracking_number' => $shippingResult['courier']['waybill_id'] ?? null,
            ]);

            \Log::info('Shipping tracking number extracted', [
                'order_id' => $order->id,
                'waybill_id' => $shippingResult['courier']['waybill_id'] ?? 'NOT_FOUND',
                'tracking_id' => $shippingResult['courier']['tracking_id'] ?? 'NOT_FOUND'
            ]);

            \Log::info('Shipping order created from admin', [
                'order_id' => $order->id,
                'shipping_order_id' => $shippingResult['id'] ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to create shipping order from admin', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'order_data' => $orderData
            ]);

            // Re-throw with more context
            throw new \Exception('Gagal membuat order pengiriman: ' . $e->getMessage());
        }
    }

    public static function updateShippingStatus(Order $order): void
    {
        $order->updateShippingStatus();
    }

    private static function mapCourierServiceCode($shippingMethod): string
    {
        // Since we now store complete pricing object from Biteship API,
        // we can directly use the courier_service_code field
        if (isset($shippingMethod['courier_service_code']) && !empty($shippingMethod['courier_service_code'])) {
            return $shippingMethod['courier_service_code'];
        }

        // Fallback: use 'type' field if courier_service_code is not available
        if (isset($shippingMethod['type']) && !empty($shippingMethod['type'])) {
            return $shippingMethod['type'];
        }

        // Final fallback: default to 'reg' (regular service)
        return 'reg';
    }
}
