<?php

namespace App\Exports;

use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersSummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private readonly array $orderIds,
    ) {
    }

    public function collection(): Collection
    {
        return Order::query()
            ->with(['user', 'items'])
            ->whereIn('id', $this->orderIds)
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Pesanan',
            'No Pesanan',
            'User',
            'Email User',
            'Penerima',
            'Telepon',
            'Tipe Pesanan',
            'Jumlah Item',
            'Subtotal',
            'Diskon Voucher',
            'Ongkir',
            'Total Pembayaran',
            'Status Pesanan',
            'Status Pembayaran',
            'Status Pengiriman',
            'No Resi',
            'Kurir',
            'Kode Voucher',
        ];
    }

    public function map($order): array
    {
        $orderStatus = OrderStatusService::getOrderStatus(
            $order->payment_status,
            $order->is_approved,
            $order->shipping_status
        );

        return [
            optional($order->created_at)?->format('d-m-Y H:i:s'),
            $order->order_number,
            $order->user?->name,
            $order->user?->email,
            $order->recipient_name,
            $order->phone,
            $order->getOrderType(),
            $order->items->sum('quantity'),
            (float) ($order->subtotal ?? 0),
            (float) ($order->voucher_discount ?? 0),
            (float) ($order->shipping_cost ?? 0),
            (float) ($order->total_amount ?? 0),
            OrderStatusService::getOrderStatusLabel($orderStatus),
            OrderStatusService::getPaymentStatusLabel($order->payment_status),
            $order->shipping_status ?? '-',
            $order->shipping_tracking_number ?? '-',
            $this->extractCourierLabel($order->shipping_method_detail),
            $order->voucher_code ?? '-',
        ];
    }

    private function extractCourierLabel($shippingMethodDetail): string
    {
        if (blank($shippingMethodDetail)) {
            return '-';
        }

        $data = $shippingMethodDetail;

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data)) {
            return '-';
        }

        $courierName = $data['courier_name'] ?? '-';
        $serviceName = $data['courier_service_name'] ?? $data['service'] ?? null;

        return $serviceName ? "{$courierName} - {$serviceName}" : $courierName;
    }
}
