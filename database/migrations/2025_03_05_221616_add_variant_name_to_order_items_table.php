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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('variant_name')->nullable()->after('product_name');
            $table->string('variant_type1')->nullable()->after('variant_name');
            $table->string('variant_option1')->nullable()->after('variant_type1');
            $table->string('variant_type2')->nullable()->after('variant_option1');
            $table->string('variant_option2')->nullable()->after('variant_type2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'variant_name',
                'variant_type1',
                'variant_option1',
                'variant_type2',
                'variant_option2'
            ]);
        });
    }
};
