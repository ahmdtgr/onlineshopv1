<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantOption extends Model
{
    protected $fillable = [
        'variant_type_id',
        'name',
        'description',
    ];

    /**
     * Get the variant type for this option.
     */
    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }
}