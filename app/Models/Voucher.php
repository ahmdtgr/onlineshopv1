<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'started_at',
        'ended_at',
        'type',
        'value',
        'max_amount',
        'min_amount',
        'is_unlimited_use',
        'max_used',
        'used',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'value' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_unlimited_use' => 'boolean',
        'used' => 'integer',
        'max_used' => 'integer',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->started_at && $now->lt($this->started_at)) {
            return false;
        }

        if ($this->ended_at && $now->gt($this->ended_at)) {
            return false;
        }

        if (!$this->is_unlimited_use && $this->max_used && $this->used >= $this->max_used) {
            return false;
        }

        return true;
    }

    public function canBeUsed(): bool
    {
        return $this->isValid();
    }

    public function incrementUsage(): void
    {
        $this->increment('used');
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        }

        // percentage type
        $discount = ($amount * $this->value) / 100;
        
        if ($this->max_amount) {
            $discount = min($discount, $this->max_amount);
        }

        return $discount;
    }
}

