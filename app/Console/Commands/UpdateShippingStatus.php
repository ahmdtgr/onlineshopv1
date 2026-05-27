<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Store;
use App\Services\BiteshipService;

class UpdateShippingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipping:update-status {--order-id= : Specific order ID to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update shipping status from Biteship API for all pending orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting shipping status update...');

        $query = Order::whereNotNull('shipping_order_id')
            ->whereIn('shipping_status', ['pending', 'confirmed', 'allocated', 'picking_up', 'picked_up', 'dropping_off']);

        // If specific order ID is provided
        if ($orderId = $this->option('order-id')) {
            $query->where('id', $orderId);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to update.');
            return;
        }

        $store = Store::first();
        if (!$store || !$store->shipping_api_key) {
            $this->error('Store or shipping API key not found.');
            return;
        }

        $shipping = new BiteshipService($store->shipping_api_key);
        $updated = 0;
        $failed = 0;

        foreach ($orders as $order) {
            try {
                $this->info("Updating order {$order->order_number} (Shipping ID: {$order->shipping_order_id})");
                
                $trackingData = $shipping->trackOrder($order->shipping_order_id);
                
                $newStatus = $trackingData['status'] ?? $order->shipping_status;
                $waybillId = $trackingData['waybill_id'] ?? $order->shipping_tracking_number;
                
                // Update order if status has changed
                if ($newStatus !== $order->shipping_status || $waybillId !== $order->shipping_tracking_number) {
                    $order->update([
                        'shipping_status' => $newStatus,
                        'shipping_tracking_number' => $waybillId,
                        'shipping_order_data' => json_encode($trackingData)
                    ]);
                    
                    $this->info("  ✓ Updated: {$order->shipping_status} → {$newStatus}");
                    
                    // Note: Order status is now determined by combination of payment_status, is_approved, and shipping_status
                    // No need to update main order status - it's computed dynamically
                    if ($newStatus === 'delivered') {
                        $this->info("  ✓ Order delivered!");
                    }
                    
                    $updated++;
                } else {
                    $this->info("  - No changes for order {$order->order_number}");
                }
                
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to update order {$order->order_number}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Orders processed: " . $orders->count());
        $this->info("Successfully updated: {$updated}");
        $this->info("Failed: {$failed}");
        
        if ($failed > 0) {
            $this->warn("Some orders failed to update. Check logs for details.");
        }
    }
}
