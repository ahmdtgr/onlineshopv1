<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\EmailNotificationService;
use App\Mail\OrderStatusChanged;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Send email when order is created
        if (EmailNotificationService::shouldSendOnOrderCreated() && $order->user && $order->user->email) {
            try {
                \Log::info('Sending order created email', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'recipient_email' => $order->user->email,
                    'status' => 'pending'
                ]);

                Mail::to($order->user->email)->send(new OrderStatusChanged($order, 'pending'));

                \Log::info('Order created email sent successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'recipient_email' => $order->user->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send order created email', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'recipient_email' => $order->user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            \Log::info('Order created email skipped', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => !EmailNotificationService::shouldSendOnOrderCreated() ? 'disabled in settings' : 'no user email'
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if status has changed
        if ($order->isDirty('status')) {
            $newStatus = $order->status;
            $oldStatus = $order->getOriginal('status');

            // Only send email if status actually changed and user has email
            if ($newStatus !== $oldStatus && $order->user && $order->user->email) {
                $this->sendStatusEmail($order, $newStatus);
            }
        }

        // Check if payment status changed to paid
        if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
            $oldPaymentStatus = $order->getOriginal('payment_status');

            if ($oldPaymentStatus !== 'paid' && EmailNotificationService::shouldSendOnOrderPaid() && $order->user && $order->user->email) {
                try {
                    \Log::info('Sending order paid email', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'recipient_email' => $order->user->email,
                        'old_payment_status' => $oldPaymentStatus,
                        'new_payment_status' => 'paid'
                    ]);

                    Mail::to($order->user->email)->send(new OrderStatusChanged($order, 'paid'));

                    \Log::info('Order paid email sent successfully', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send order paid email', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Check if order is approved
        if ($order->isDirty('is_approved') && $order->is_approved) {
            $wasApproved = $order->getOriginal('is_approved');

            if (!$wasApproved && EmailNotificationService::shouldSendOnOrderApproved() && $order->user && $order->user->email) {
                try {
                    \Log::info('Sending order approved email', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'recipient_email' => $order->user->email
                    ]);

                    Mail::to($order->user->email)->send(new OrderStatusChanged($order, 'approved'));

                    \Log::info('Order approved email sent successfully', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send order approved email', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Check if shipping status changed to delivered
        if ($order->isDirty('shipping_status') && $order->shipping_status === 'delivered') {
            $oldShippingStatus = $order->getOriginal('shipping_status');

            if ($oldShippingStatus !== 'delivered' && EmailNotificationService::shouldSendOnOrderDelivered() && $order->user && $order->user->email) {
                try {
                    \Log::info('Sending order delivered email', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'recipient_email' => $order->user->email,
                        'old_shipping_status' => $oldShippingStatus,
                        'new_shipping_status' => 'delivered'
                    ]);

                    Mail::to($order->user->email)->send(new OrderStatusChanged($order, 'delivered'));

                    \Log::info('Order delivered email sent successfully', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send order delivered email', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }
    }

    /**
     * Send email based on status
     */
    protected function sendStatusEmail(Order $order, string $status): void
    {
        try {
            $shouldSend = false;

            switch ($status) {
                case 'processing':
                case 'awaiting_confirmation':
                    $shouldSend = EmailNotificationService::shouldSendOnOrderProcessing();
                    break;
                case 'shipped':
                    $shouldSend = EmailNotificationService::shouldSendOnOrderShipped();
                    break;
                case 'completed':
                    $shouldSend = EmailNotificationService::shouldSendOnOrderDelivered();
                    break;
                case 'cancelled':
                    $shouldSend = EmailNotificationService::shouldSendOnOrderCancelled();
                    break;
            }

            if ($shouldSend && $order->user && $order->user->email) {
                \Log::info('Sending order status change email', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'recipient_email' => $order->user->email,
                    'status' => $status,
                    'old_status' => $order->getOriginal('status')
                ]);

                Mail::to($order->user->email)->send(new OrderStatusChanged($order, $status));

                \Log::info('Order status change email sent successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $status
                ]);
            } else {
                \Log::info('Order status email skipped', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $status,
                    'reason' => !$shouldSend ? 'disabled in settings' : 'no user email'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send order status email', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
