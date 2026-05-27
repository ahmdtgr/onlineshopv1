<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantType extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * Get the variant options for this type.
     */
    public function variantOptions(): HasMany
    {
        return $this->hasMany(VariantOption::class);
    }
    
    /**
     * Get product variants using this type as first variant.
     */
    public function productVariantsAsType1(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'variant_type1', 'name');
    }
    
    /**
     * Get product variants using this type as second variant.
     */
    public function productVariantsAsType2(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'variant_type2', 'name');
    }
}