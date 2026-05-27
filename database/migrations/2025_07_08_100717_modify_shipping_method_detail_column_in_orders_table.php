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
            // Change shipping_method_detail from string to longText to store complete API response
            $table->longText('shipping_method_detail')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rollback: Change shipping_method_detail back to string
            $table->string('shipping_method_detail')->nullable()->change();
        });
    }
};
