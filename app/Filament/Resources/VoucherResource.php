<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Voucher';

    protected static ?string $modelLabel = 'Voucher';

    protected static ?string $pluralModelLabel = 'Voucher';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Voucher')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Voucher')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Voucher')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function ($set) {
                                        $set('code', strtoupper(substr(md5(uniqid()), 0, 8)));
                                    })
                            ),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Periode Voucher')
                    ->schema([
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('ended_at')
                            ->label('Tanggal Berakhir')
                            ->nullable()
                            ->after('started_at'),
                    ])->columns(2),

                Forms\Components\Section::make('Nilai Diskon')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipe Diskon')
                            ->options([
                                'fixed' => 'Nominal Tetap',
                                'percentage' => 'Persentase',
                            ])
                            ->required()
                            ->reactive()
                            ->default('fixed'),
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->required()
                            ->numeric()
                            ->prefix(fn($get) => $get('type') === 'fixed' ? 'Rp' : '')
                            ->suffix(fn($get) => $get('type') === 'percentage' ? '%' : '')
                            ->minValue(0)
                            ->helperText(fn($get) => $get('type') === 'percentage' ? 'Masukkan nilai 0-100 untuk persentase' : 'Masukkan nominal diskon'),
                        Forms\Components\TextInput::make('max_amount')
                            ->label('Maksimal Diskon')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable()
                            ->helperText('Kosongkan jika tidak ada batas maksimal')
                            ->hidden(fn($get) => $get('type') === 'fixed'),
                        Forms\Components\TextInput::make('min_amount')
                            ->label('Minimal Pembelian')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable()
                            ->helperText('Kosongkan jika tidak ada batas minimal'),
                    ])->columns(2),

                Forms\Components\Section::make('Batas Penggunaan')
                    ->schema([
                        Forms\Components\Toggle::make('is_unlimited_use')
                            ->label('Penggunaan Unlimited')
                            ->default(false)
                            ->inline(false)
                            ->reactive()
                            ->required(),
                        Forms\Components\TextInput::make('max_used')
                            ->label('Maksimal Penggunaan')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->required(fn($get) => !$get('is_unlimited_use'))
                            ->hidden(fn($get) => $get('is_unlimited_use'))
                            ->helperText('Berapa kali voucher ini dapat digunakan'),
                        Forms\Components\TextInput::make('used')
                            ->label('Sudah Digunakan')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Voucher')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'fixed' => 'Nominal',
                        'percentage' => 'Persentase',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'fixed' => 'success',
                        'percentage' => 'info',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->formatStateUsing(
                        fn($record) => $record->type === 'fixed'
                        ? 'Rp ' . number_format($record->value, 0, ',', '.')
                        : $record->value . '%'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_amount')
                    ->label('Min. Pembelian')
                    ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ended_at')
                    ->label('Berakhir')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Tidak terbatas')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('usage')
                    ->label('Penggunaan')
                    ->state(function ($record) {
                        return $record->is_unlimited_use
                            ? $record->used . ' / ∞'
                            : $record->used . ' / ' . ($record->max_used ?? 0);
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->is_unlimited_use)
                            return 'success';
                        $percentage = $record->max_used > 0 ? ($record->used / $record->max_used) * 100 : 0;
                        return $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'fixed' => 'Nominal',
                        'percentage' => 'Persentase',
                    ]),
                Tables\Filters\Filter::make('valid')
                    ->label('Voucher Valid')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('is_active', true)
                            ->where('started_at', '<=', now())
                            ->where(function ($q) {
                                $q->whereNull('ended_at')
                                    ->orWhere('ended_at', '>=', now());
                            })
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
