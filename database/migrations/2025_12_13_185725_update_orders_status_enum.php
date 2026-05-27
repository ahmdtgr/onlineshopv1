<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing data based on payment_status, is_approved, and shipping_status
        DB::statement("
            UPDATE orders 
            SET status = CASE
                WHEN payment_status = 'unpaid' THEN 'pending'
                WHEN payment_status = 'paid' AND is_approved = 0 THEN 'awaiting_confirmation'
                WHEN payment_status = 'paid' AND is_approved = 1 AND (shipping_status IS NULL OR shipping_status IN ('pending', 'confirmed', 'scheduled', 'allocated', 'picking_up', 'picked')) THEN 'processing'
                WHEN shipping_status = 'dropping_off' THEN 'shipped'
                WHEN shipping_status = 'delivered' THEN 'completed'
                WHEN shipping_status IN ('cancelled', 'on_hold', 'rejected', 'courier_not_found', 'return_in_transit', 'returned', 'disposed') THEN 'cancelled'
                ELSE status
            END
        ");
        
        // Modify the enum column to include awaiting_confirmation
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'awaiting_confirmation', 'processing', 'shipped', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert awaiting_confirmation to pending
        DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'awaiting_confirmation'");
        
        // Revert the enum column
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'shipped', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
