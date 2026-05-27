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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('value', 15, 2);
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->decimal('min_amount', 15, 2)->nullable();
            $table->boolean('is_unlimited_use')->default(false);
            $table->integer('max_used')->nullable();
            $table->integer('used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
