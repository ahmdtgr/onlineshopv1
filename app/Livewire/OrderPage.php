<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Services\ShippingStatusService;
use App\Services\OrderStatusService;

class OrderPage extends Component
{
    public $statusFilter = 'all';
    public $searchQuery = '';

    public function getStatusClass($shippingStatus)
    {
        $simplified = ShippingStatusService::getSimplifiedStatus($shippingStatus);
        return ShippingStatusService::getShippingStatusColor($simplified);
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function getOrders()
    {
        $query = Order::with('items')
                    ->where('user_id', auth()->id());
        
        // Filter by search query
        if (!empty($this->searchQuery)) {
            $query->where('order_number', 'like', '%' . $this->searchQuery . '%');
        }
        
        // Filter by status
        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'pending':
                    $query->where('status', 'pending');
                    break;
                case 'awaiting_confirmation':
                    $query->where('status', 'awaiting_confirmation');
                    break;
                case 'processing':
                    $query->where('status', 'processing');
                    break;
                case 'shipped':
                    $query->where('status', 'shipped');
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
                case 'cancelled':
                    $query->where('status', 'cancelled');
                    break;
            }
        }
        
        return $query->orderBy('id', 'DESC')->get();
    }

    public function render()
    {
        return view('livewire.order', [
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
