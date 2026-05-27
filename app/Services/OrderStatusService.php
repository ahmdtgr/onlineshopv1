<?php

namespace App\Services;

use Carbon\Carbon;

class OrderStatusService
{
    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_AWAITING_CONFIRMATION = 'awaiting_confirmation';

    // Payment Status Constants
    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID = 'paid';

    public static function getStatusLabel($status): string
    {
        return match($status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_AWAITING_CONFIRMATION => 'Menunggu Konfirmasi',
            self::STATUS_PROCESSING => 'Dikemas',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Status Tidak Diketahui'
        };
    }

    public static function getStatusColor($status): string
    {
        return match($status) {
            self::STATUS_PENDING => 'text-orange-500',
            self::STATUS_AWAITING_CONFIRMATION => 'text-yellow-500',
            self::STATUS_PROCESSING => 'text-blue-500',
            self::STATUS_SHIPPED => 'text-blue-500',
            self::STATUS_COMPLETED => 'text-green-500',
            self::STATUS_CANCELLED => 'text-red-500',
            default => 'text-gray-500'
        };
    }

    public static function getStatusInfo($status, $paymentDeadline = null, $completedAt = null): array
    {
        return match($status) {
            self::STATUS_PENDING => [
                'icon' => 'bi-clock-fill',
                'color' => 'orange',
                'title' => 'Menunggu Pembayaran',
                'message' => $paymentDeadline ? 
                    'Selesaikan pembayaran sebelum ' . $paymentDeadline->format('d M Y H:i') :
                    'Selesaikan pembayaran'
            ],
            self::STATUS_AWAITING_CONFIRMATION => [
                'icon' => 'bi-hourglass-split',
                'color' => 'yellow',
                'title' => 'Menunggu Konfirmasi',
                'message' => 'Pesanan menunggu konfirmasi dari admin/penjual'
            ],
            self::STATUS_PROCESSING => [
                'icon' => 'bi-box-seam-fill',
                'color' => 'blue',
                'title' => 'Pesanan Dikemas',
                'message' => 'Penjual sedang menyiapkan pesanan Anda'
            ],
            self::STATUS_SHIPPED => [
                'icon' => 'bi-truck',
                'color' => 'green',
                'title' => 'Dalam Pengiriman',
                'message' => 'Pesanan sedang dalam perjalanan'
            ],
            self::STATUS_COMPLETED => [
                'icon' => 'bi-check-circle-fill',
                'color' => 'green',
                'title' => 'Pesanan Selesai',
                'message' => $completedAt ? 
                    'Pesanan telah diterima pada ' . Carbon::parse($completedAt)->format('d M Y H:i') :
                    'Pesanan telah selesai'
            ],
            self::STATUS_CANCELLED => [
                'icon' => 'bi-x-circle-fill',
                'color' => 'red',
                'title' => 'Pesanan Dibatalkan',
                'message' => 'Pesanan telah dibatalkan'
            ],
            default => [
                'icon' => 'bi-info-circle-fill',
                'color' => 'gray',
                'title' => 'Status Tidak Diketahui',
                'message' => ''
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

    /**
     * Get order status based on payment_status, is_approved, and shipping_status combination
     */
    public static function getOrderStatus($paymentStatus, $isApproved, $shippingStatus): string
    {
        // Jika belum bayar, status pending
        if ($paymentStatus === self::PAYMENT_UNPAID) {
            return self::STATUS_PENDING;
        }

        // Jika sudah bayar tapi belum approved, menunggu konfirmasi
        if ($paymentStatus === self::PAYMENT_PAID && !$isApproved) {
            return self::STATUS_AWAITING_CONFIRMATION;
        }

        // Jika sudah approved tapi belum ada shipping status, sedang dipersiapkan
        if ($paymentStatus === self::PAYMENT_PAID && $isApproved && (!$shippingStatus || $shippingStatus === 'pending')) {
            return self::STATUS_PROCESSING;
        }

        // Jika sudah ada shipping status, ikuti status pengiriman
        if ($paymentStatus === self::PAYMENT_PAID && $isApproved && $shippingStatus) {
            return match($shippingStatus) {
                'confirmed', 'allocated' => self::STATUS_PROCESSING,
                'picking_up', 'picked_up', 'dropping_off' => self::STATUS_SHIPPED,
                'delivered' => self::STATUS_COMPLETED,
                'cancelled', 'returned' => self::STATUS_CANCELLED,
                default => self::STATUS_PROCESSING
            };
        }

        return self::STATUS_PENDING;
    }

    /**
     * Get order status label based on status only
     */
    public static function getOrderStatusLabel($status): string
    {
        return self::getStatusLabel($status);
    }

    /**
     * Get order status color based on combination
     */
    public static function getOrderStatusColor($paymentStatus, $isApproved, $shippingStatus): string
    {
        $status = self::getOrderStatus($paymentStatus, $isApproved, $shippingStatus);
        
        return match($status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_AWAITING_CONFIRMATION => 'gray',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_SHIPPED => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'gray'
        };
    }
}