<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_id',
        'voucher_code',
        'voucher_name',
        'voucher_discount',
        'order_number',
        'subtotal',
        'total_amount',
        'status',
        'payment_status',
        'paid_at',
        'is_approved',
        'recipient_name',
        'phone',
        'shipping_provider',
        'shipping_cost',
        'shipping_area_id',
        'shipping_area_name',
        'shipping_address',
        'shipping_method_detail',
        'shipping_tracking_number',
        'shipping_order_id',
        'shipping_order_data',
        'shipping_status',
        'noted',
        'payment_gateway_transaction_id',
        'payment_gateway_data',
        'payment_proof'
    ];

    protected $casts = [
        'shipping_method_detail' => 'array',
        'shipping_order_data' => 'array',
        'payment_gateway_data' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get order type based on products (digital/physical/mixed)
     */
    public function getOrderType(): string
    {
        $items = $this->items()->with('product')->get();

        if ($items->isEmpty()) {
            return 'unknown';
        }

        $hasDigital = false;
        $hasPhysical = false;

        foreach ($items as $item) {
            if ($item->product && $item->product->is_product_digital) {
                $hasDigital = true;
            } else {
                $hasPhysical = true;
            }

            // Early exit if we found both types
            if ($hasDigital && $hasPhysical) {
                return 'mixed';
            }
        }

        if ($hasDigital && !$hasPhysical) {
            return 'digital';
        }

        if ($hasPhysical && !$hasDigital) {
            return 'physical';
        }

        return 'unknown';
    }

    /**
     * Update shipping status using Biteship API
     */
    public function updateShippingStatus(): void
    {
        if (!$this->shipping_order_id) {
            throw new \Exception('Order belum memiliki shipping order ID');
        }

        $store = \App\Models\Store::first();
        if (!$store || !$store->shipping_api_key) {
            throw new \Exception('Store atau API key shipping tidak ditemukan');
        }

        $shipping = new \App\Services\BiteshipService($store->shipping_api_key);
        $trackingData = $shipping->trackOrder($this->shipping_order_id);

        $newStatus = $trackingData['status'] ?? $this->shipping_status;
        $waybillId = $trackingData['courier']['waybill_id'] ?? $this->shipping_tracking_number;

        // Prepare update data
        $updateData = [
            'shipping_status' => $newStatus,
            'shipping_tracking_number' => $waybillId,
            'shipping_order_data' => json_encode($trackingData)
        ];

        // Auto-update order status to completed if package is delivered
        if ($newStatus === 'delivered' && $this->status !== 'completed') {
            $updateData['status'] = 'completed';
        }

        $this->update($updateData);

        \Log::info('Shipping status updated', [
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'old_status' => $this->shipping_status,
            'new_status' => $newStatus,
            'context' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown'
        ]);
    }

    /**
     * Update shipping status only if needed (not in final states)
     */
    public function updateShippingStatusIfNeeded(): void
    {
        // Only update if order has shipping_order_id and is not in final states
        if (!$this->shipping_order_id) {
            return;
        }

        // Skip update if already in final states
        $finalStates = ['delivered', 'cancelled', 'returned', 'disposed'];
        if (in_array($this->shipping_status, $finalStates)) {
            return;
        }

        try {
            $this->updateShippingStatus();
        } catch (\Exception $e) {
            \Log::error('Failed to update shipping status', [
                'order_id' => $this->id,
                'order_number' => $this->order_number,
                'error' => $e->getMessage(),
                'context' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown'
            ]);

            // Re-throw for admin context, silence for customer context
            $context = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? '';
            if (strpos($context, 'OrderResource') !== false) {
                throw $e;
            }
        }
    }
}
