<?php

namespace App\Services;

use Carbon\Carbon;

class ShippingStatusService
{
    // Shipping Status Constants from Biteship - Categorized
    const STATUS_PENDING = 'pending';
    
    // Preparation Phase
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SCHEDULED = 'scheduled'; 
    const STATUS_ALLOCATED = 'allocated';
    
    // Pickup Phase
    const STATUS_PICKING_UP = 'picking_up';
    const STATUS_PICKED = 'picked';
    
    // Transit Phase
    const STATUS_DROPPING_OFF = 'dropping_off';
    
    // Success
    const STATUS_DELIVERED = 'delivered';
    
    // Failed/Cancelled
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COURIER_NOT_FOUND = 'courier_not_found';
    
    // Returned
    const STATUS_RETURN_IN_TRANSIT = 'return_in_transit';
    const STATUS_RETURNED = 'returned';
    const STATUS_DISPOSED = 'disposed';

    // Payment Status Constants
    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID = 'paid';

    /**
     * Get simplified category for display
     */
    public static function getSimplifiedStatus($status): string
    {
        return match($status) {
            null => 'pending',
            self::STATUS_PENDING => 'pending',
            
            // Preparation Phase
            self::STATUS_CONFIRMED,
            self::STATUS_SCHEDULED,
            self::STATUS_ALLOCATED => 'preparing',
            
            // Pickup Phase  
            self::STATUS_PICKING_UP,
            self::STATUS_PICKED => 'pickup',
            
            // Transit Phase
            self::STATUS_DROPPING_OFF => 'transit',
            
            // Success
            self::STATUS_DELIVERED => 'delivered',
            
            // Failed/Cancelled
            self::STATUS_CANCELLED,
            self::STATUS_ON_HOLD,
            self::STATUS_REJECTED,
            self::STATUS_COURIER_NOT_FOUND => 'cancelled',
            
            // Returned
            self::STATUS_RETURN_IN_TRANSIT,
            self::STATUS_RETURNED,
            self::STATUS_DISPOSED => 'returned',
            
            default => 'unknown'
        };
    }

    public static function getShippingStatusLabel($status): string
    {
        $simplified = self::getSimplifiedStatus($status);
        
        return match($simplified) {
            'pending' => 'Belum Diproses',
            'preparing' => 'Sedang Dipersiapkan',
            'pickup' => 'Proses Pickup',
            'transit' => 'Dalam Pengiriman',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            'returned' => 'Dikembalikan',
            default => 'Status Tidak Diketahui'
        };
    }

    public static function getDetailedStatusLabel($status): string
    {
        return match($status) {
            null => 'Belum Diproses',
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_SCHEDULED => 'Dijadwalkan',
            self::STATUS_ALLOCATED => 'Dialokasikan',
            self::STATUS_PICKING_UP => 'Sedang Pickup',
            self::STATUS_PICKED => 'Sudah Dipickup',
            self::STATUS_DROPPING_OFF => 'Dalam Pengiriman',
            self::STATUS_DELIVERED => 'Terkirim',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_ON_HOLD => 'Ditahan',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_COURIER_NOT_FOUND => 'Kurir Tidak Ditemukan',
            self::STATUS_RETURN_IN_TRANSIT => 'Dalam Proses Return',
            self::STATUS_RETURNED => 'Dikembalikan',
            self::STATUS_DISPOSED => 'Dimusnahkan',
            default => ucfirst(str_replace('_', ' ', $status ?? ''))
        };
    }

    public static function getShippingStatusColor($status): string
    {
        $simplified = self::getSimplifiedStatus($status);
        
        return match($simplified) {
            'pending' => 'text-orange-500',
            'preparing' => 'text-blue-500',
            'pickup' => 'text-purple-500',
            'transit' => 'text-blue-600',
            'delivered' => 'text-green-500',
            'cancelled' => 'text-red-500',
            'returned' => 'text-red-500',
            default => 'text-gray-500'
        };
    }

    public static function getShippingStatusInfo($shippingStatus, $paymentStatus = null, $paymentDeadline = null, $deliveredAt = null): array
    {
        // If payment not completed, show payment status
        if ($paymentStatus === self::PAYMENT_UNPAID) {
            return [
                'icon' => 'bi-clock-fill',
                'color' => 'orange',
                'title' => 'Menunggu Pembayaran',
                'message' => $paymentDeadline ? 
                    'Selesaikan pembayaran sebelum ' . $paymentDeadline->format('d M Y H:i') :
                    'Selesaikan pembayaran'
            ];
        }

        // If payment completed but no shipping status yet
        if (is_null($shippingStatus) && $paymentStatus === self::PAYMENT_PAID) {
            return [
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => 'warning',
                'title' => 'Menunggu Konfirmasi',
                'message' => 'Pesanan Anda sedang menunggu konfirmasi dari seller'
            ];
        }

        $simplified = self::getSimplifiedStatus($shippingStatus);
        
        return match($simplified) {
            'pending' => [
                'icon' => 'bi-clock-fill',
                'color' => 'orange',
                'title' => 'Menunggu Proses',
                'message' => 'Pesanan menunggu untuk diproses'
            ],
            'preparing' => [
                'icon' => 'bi-box-seam-fill',
                'color' => 'blue',
                'title' => 'Sedang Dipersiapkan',
                'message' => 'Pesanan sedang disiapkan dan kurir dialokasikan'
            ],
            'pickup' => [
                'icon' => 'bi-arrow-up-square-fill',
                'color' => 'purple',
                'title' => 'Proses Pickup',
                'message' => 'Kurir sedang atau sudah mengambil paket'
            ],
            'transit' => [
                'icon' => 'bi-truck',
                'color' => 'blue',
                'title' => 'Dalam Pengiriman',
                'message' => 'Paket sedang dalam perjalanan ke alamat tujuan'
            ],
            'delivered' => [
                'icon' => 'bi-check-circle-fill',
                'color' => 'green',
                'title' => 'Paket Terkirim',
                'message' => $deliveredAt ? 
                    'Paket telah diterima pada ' . Carbon::parse($deliveredAt)->format('d M Y H:i') :
                    'Paket telah berhasil diterima'
            ],
            'cancelled' => [
                'icon' => 'bi-x-circle-fill',
                'color' => 'red',
                'title' => 'Pengiriman Dibatalkan',
                'message' => 'Pengiriman pesanan telah dibatalkan atau ditolak'
            ],
            'returned' => [
                'icon' => 'bi-arrow-return-left',
                'color' => 'red',
                'title' => 'Paket Dikembalikan',
                'message' => 'Paket telah dikembalikan ke pengirim'
            ],
            default => [
                'icon' => 'bi-info-circle-fill',
                'color' => 'gray',
                'title' => 'Status Tidak Diketahui',
                'message' => 'Status pengiriman tidak dapat ditentukan'
            ]
        };
    }

    public static function getPaymentStatusLabel($status): string
    {
        return match($status) {
            self::PAYMENT_UNPAID => 'Belum Dibayar',
            self::PAYMENT_PAID => 'Sudah Dibayar',
            default => 'Status Pembayaran Tidak Diketahui'
        };
    }

    public static function getPaymentStatusColor($status): string
    {
        return match($status) {
            self::PAYMENT_UNPAID => 'text-red-500',
            self::PAYMENT_PAID => 'text-green-500',
            default => 'text-gray-500'
        };
    }

    public static function canCancelOrder($shippingStatus, $paymentStatus): bool
    {
        // Can only cancel if payment is unpaid or shipping hasn't progressed too far
        if ($paymentStatus === self::PAYMENT_UNPAID) {
            return true;
        }
        
        $simplified = self::getSimplifiedStatus($shippingStatus);
        return in_array($simplified, ['pending', 'preparing']);
    }

    /**
     * Check if order can be deleted (from Biteship table)
     */
    public static function canDeleteFromBiteship($status): bool
    {
        return in_array($status, [
            self::STATUS_CONFIRMED,
            self::STATUS_SCHEDULED,
            self::STATUS_ALLOCATED,
            self::STATUS_PICKING_UP
        ]);
    }

    // Helper function untuk status display ke customer
    public function getCustomerStatusAttribute()
    {
        return match($this->status) {
            'pending_approval' => 'Menunggu Konfirmasi',
            'approved' => 'Sedang Dipersiapkan', 
            'processing' => 'Sedang Diproses',
            'shipped' => $this->getSimplifiedShippingStatus() ?? 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Status Tidak Diketahui'
        };
    }
}