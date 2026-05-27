<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_type1',
        'variant_option1',
        'variant_type2',
        'variant_option2',
        'sku',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the first variant type.
     */
    public function variantType1(): BelongsTo
    {
        return $this->belongsTo(VariantType::class, 'variant_type1', 'name');
    }
    
    /**
     * Get the second variant type.
     */
    public function variantType2(): BelongsTo
    {
        return $this->belongsTo(VariantType::class, 'variant_type2', 'name');
    }
    
    /**
     * Get formatted variant name.
     */
    public function getVariantNameAttribute(): string
    {
        $parts = [];
        
        if ($this->variant_type1 && $this->variant_option1) {
            $parts[] = $this->variant_type1 . ': ' . $this->variant_option1;
        }
        
        if ($this->variant_type2 && $this->variant_option2) {
            $parts[] = $this->variant_type2 . ': ' . $this->variant_option2;
        }
        
        return implode(' / ', $parts);
    }
    
    /**
     * Generate SKU dari product slug dan opsi varian.
     * Metode sederhana dan cepat untuk menghasilkan SKU.
     */
    public static function generateSku(string $productSlug, string $option1 = null, string $option2 = null): string
    {
        // Base SKU from product
        $base = strtoupper(substr($productSlug, 0, 4));
        
        // Add random part to ensure uniqueness
        $random = strtoupper(Str::random(4));
        
        // Create SKU
        return $base . '-' . $random;
    }
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($variant) {
            // Auto-generate SKU if not provided
            if (empty($variant->sku)) {
                $variant->sku = self::generateSku(
                    $variant->product->slug ?? 'PROD',
                    $variant->variant_option1 ?? null,
                    $variant->variant_option2 ?? null
                );
            }
        });
    }
}