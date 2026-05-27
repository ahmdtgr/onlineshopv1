<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function handle(Request $request)
    {
        try {
            // Get notification data from Midtrans
            $notification = $request->all();
            
            Log::info('Midtrans Notification Received', $notification);

            // Verify notification signature
            $serverKey = config('services.midtrans.server_key');
            $orderId = $notification['order_id'] ?? null;
            $statusCode = $notification['status_code'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $signatureKey = $notification['signature_key'] ?? null;

            // Create hash for verification
            $mySignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            // Verify signature
            if ($signatureKey !== $mySignatureKey) {
                Log::warning('Invalid Midtrans signature', [
                    'order_id' => $orderId,
                    'expected' => $mySignatureKey,
                    'received' => $signatureKey
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // Find order by order_number
            $order = Order::where('order_number', $orderId)->first();

            if (!$order) {
                Log::warning('Order not found for Midtrans notification', ['order_id' => $orderId]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Get transaction status
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            // Update order status based on transaction status
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    // Payment captured successfully
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'awaiting_confirmation', // Menunggu konfirmasi admin
                        'paid_at' => now()
                    ]);
                    Log::info('Order payment captured', ['order_id' => $orderId]);
                }
            } elseif ($transactionStatus === 'settlement') {
                // Payment settled
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'awaiting_confirmation', // Menunggu konfirmasi admin
                    'paid_at' => now()
                ]);
                Log::info('Order payment settled', ['order_id' => $orderId]);
            } elseif ($transactionStatus === 'pending') {
                // Payment pending
                $order->update([
                    'payment_status' => 'unpaid',
                    'status' => 'pending'
                ]);
                Log::info('Order payment pending', ['order_id' => $orderId]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                // Payment denied/expired/cancelled
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);
                Log::info('Order payment failed', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus
                ]);
            }

            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans notification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
