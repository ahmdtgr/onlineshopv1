<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ShippingStatusService;

class Orders extends Component
{
    use WithPagination;
    
    public function getStatusClass($shippingStatus)
    {
        $simplified = ShippingStatusService::getSimplifiedStatus($shippingStatus);
        return ShippingStatusService::getShippingStatusColor($simplified);
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && ShippingStatusService::canCancelOrder($order->shipping_status, $order->payment_status)) {
            $order->update(['shipping_status' => 'cancelled']);
            session()->flash('message', 'Pesanan berhasil dibatalkan');
        }
    }

    public function getOrders()
    {
        $query = Order::with(['items', 'items.product'])
                    ->where('user_id', auth()->id())
                    ->latest();

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.orders', [
            'orders' => $this->getOrders(),
            'statusLabels' => [
                'pending' => 'Belum Diproses',
                'preparing' => 'Sedang Dipersiapkan', 
                'pickup' => 'Proses Pickup',
                'transit' => 'Dalam Pengiriman',
                'delivered' => 'Terkirim',
                'cancelled' => 'Dibatalkan',
                'returned' => 'Dikembalikan'
            ]
        ])->layout('components.layouts.app', [
            'seoTitle' => 'Pesanan Saya',
            'seoDescription' => 'Daftar pesanan Anda',
            'seoRobots' => 'noindex, nofollow',
        ]);
    }
}