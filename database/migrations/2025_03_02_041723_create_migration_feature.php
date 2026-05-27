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
        // Create variant types table
        Schema::create('variant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        
        // Create variant options table
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            
            // Each option name should be unique within its type
            // Specify a shorter custom name for the index
            $table->unique(['variant_type_id', 'name'], 'var_opt_type_name_unique');
        });
        
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('variant_type1')->nullable()->comment('Jenis varian pertama (misalnya: Ukuran)');
            $table->string('variant_option1')->nullable()->comment('Opsi varian pertama (misalnya: L)');
            $table->string('variant_type2')->nullable()->comment('Jenis varian kedua (misalnya: Warna)');
            $table->string('variant_option2')->nullable()->comment('Opsi varian kedua (misalnya: Merah)');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Memastikan kombinasi varian unik untuk setiap produk
            $table->unique(['product_id', 'variant_option1', 'variant_option2'], 'unique_variant_combination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_options');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('variant_options');
        Schema::dropIfExists('variant_types');
    }
};
