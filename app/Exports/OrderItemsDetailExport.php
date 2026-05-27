<?php

namespace App\Exports;

use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderItemsDetailExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private readonly array $orderIds,
    ) {
    }

    public function headings(): array
    {
        return [
            'Tanggal Pesanan',
            'No Pesanan',
            'User',
            'Email User',
            'Penerima',
            'Produk',
            'Varian',
            'Tipe Produk',
            'Qty',
            'Harga Satuan',
            'Subtotal Item',
            'Voucher',
            'Diskon Voucher',
            'Ongkir',
            'Total Pesanan',
            'Status Pesanan',
            'Status Pembayaran',
        ];
    }

    public function collection(): Collection
    {
        $orders = Order::query()
            ->with(['user', 'items.product'])
            ->whereIn('id', $this->orderIds)
            ->orderByDesc('created_at')
            ->get();

        return $orders->flatMap(function (Order $order) {
            $orderStatus = OrderStatusService::getOrderStatus(
                $order->payment_status,
                $order->is_approved,
                $order->shipping_status
            );

            return $order->items->map(function ($item) use ($order, $orderStatus) {
                return [
                    optional($order->created_at)?->format('d-m-Y H:i:s'),
                    $order->order_number,
                    $order->user?->name,
                    $order->user?->email,
                    $order->recipient_name,
                    $item->product_name,
                    $item->variant_name ?? '-',
                    $item->product?->is_product_digital ? 'Digital' : 'Fisik',
                    (int) ($item->quantity ?? 0),
                    (float) ($item->price ?? 0),
                    (float) (($item->price ?? 0) * ($item->quantity ?? 0)),
                    $order->voucher_code ?? '-',
                    (float) ($order->voucher_discount ?? 0),
                    (float) ($order->shipping_cost ?? 0),
                    (float) ($order->total_amount ?? 0),
                    OrderStatusService::getOrderStatusLabel($orderStatus),
                    OrderStatusService::getPaymentStatusLabel($order->payment_status),
                ];
            });
        })->values();
    }
}
