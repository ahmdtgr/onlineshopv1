<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BiteshipService;

class SyncTrackingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracking:sync {--order-id= : Sync specific order by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync tracking status from Biteship API for all orders with tracking numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $biteshipService = new BiteshipService();
        
        $orderId = $this->option('order-id');
        
        if ($orderId) {
            // Sync specific order
            $order = \App\Models\Order::find($orderId);
            
            if (!$order) {
                $this->error("Order with ID {$orderId} not found.");
                return 1;
            }
            
            if (!$order->shipping_tracking_number) {
                $this->error("Order {$orderId} has no tracking number.");
                return 1;
            }
            
            try {
                $this->info("Syncing tracking status for order {$orderId}...");
                $trackingData = $biteshipService->syncTrackingStatus($order);
                $this->info("✅ Successfully synced order {$orderId}");
                $this->info("Status: " . ($trackingData['status'] ?? 'Unknown'));
                return 0;
            } catch (\Exception $e) {
                $this->error("❌ Failed to sync order {$orderId}: " . $e->getMessage());
                return 1;
            }
        } else {
            // Sync all orders
            $this->info('Starting to sync tracking status for all orders...');
            $this->newLine();
            
            try {
                $results = $biteshipService->syncAllTrackingStatus();
                
                $this->info("📦 Sync Results:");
                $this->info("Total orders: {$results['total']}");
                $this->info("✅ Successfully synced: {$results['success']}");
                $this->info("❌ Failed: {$results['failed']}");
                
                if ($results['failed'] > 0) {
                    $this->warn("Some orders failed to sync. Check the logs for details.");
                }
                
                return 0;
            } catch (\Exception $e) {
                $this->error("Failed to sync tracking status: " . $e->getMessage());
                return 1;
            }
        }
    }
}
