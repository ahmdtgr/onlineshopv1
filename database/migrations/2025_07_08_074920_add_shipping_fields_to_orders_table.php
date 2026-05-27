<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_order_id')->nullable()->after('shipping_tracking_number');
            $table->json('shipping_order_data')->nullable()->after('shipping_order_id');
            $table->string('shipping_status')->nullable()->after('shipping_order_data');
            $table->text('noted')->nullable()->after('shipping_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_order_id', 'shipping_order_data', 'shipping_status', 'noted']);
        });
    }
};
