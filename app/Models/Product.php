<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'sku',
        'description',
        'price',
        'stock',
        'is_active',
        'is_product_digital',
        'images',
        'weight',
        'height',
        'width',
        'length',
        'has_variants',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'is_product_digital' => 'boolean',
        'has_variants' => 'boolean',
    ];

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getFirstImageUrlAttribute()
    {
        if (empty($this->images)) {
            return null;
        }

        $firstImage = is_string($this->images) ? json_decode($this->images, true)[0] : $this->images[0];


        return $firstImage ? url('storage/' . $firstImage) : null;

    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public static function generateSku(string $productName): string
    {
        $base = Str::upper(Str::substr(Str::slug($productName), 0, 8));
        $uniqueId = strtoupper(Str::random(4));
        return $base . '-' . $uniqueId;
    }

    public function getDisplayPriceAttribute()
    {
        if ($this->has_variants) {
            $lowestVariantPrice = $this->variants()
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->min('price');

            return $lowestVariantPrice ?? $this->price;
        }
        return $this->price;
    }
}
