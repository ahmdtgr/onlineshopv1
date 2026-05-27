<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('order_number')->constrained()->nullOnDelete();
            $table->string('voucher_code')->nullable()->after('voucher_id');
            $table->string('voucher_name')->nullable()->after('voucher_code');
            $table->decimal('voucher_discount', 15, 2)->default(0)->after('voucher_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'voucher_code', 'voucher_name', 'voucher_discount']);
        });
    }
};
