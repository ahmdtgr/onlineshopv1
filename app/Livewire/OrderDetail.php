<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\Store;
use Carbon\Carbon;
use App\Models\Review;
use App\Services\ShippingStatusService;
use App\Services\OrderStatusService;
use App\Services\MidtransService;
use App\Services\BiteshipService;

class OrderDetail extends Component
{
    use WithFileUploads;

    public $order;
    public $paymentDeadline;
    public $paymentMethods;
    public $store;
    public $trackingInfo = null;
    public $isLoadingTracking = true; // Set true as default

    // Review properties
    public $reviewRatings = [];
    public $reviewTexts = [];
    public $reviewImages = [];
    public $editingReviewItemId = null;

    public function mount($orderNumber)
    {
        $this->order = Order::where('order_number', $orderNumber)
            ->with(['items', 'items.product', 'items.review'])
            ->firstOrFail();
        $this->paymentDeadline = Carbon::parse($this->order->created_at)->addHours(12);
        $this->paymentMethods = PaymentMethod::all();

        // Auto-update shipping status when user accesses order detail
        $this->updateShippingStatusIfNeeded();

        // Don't load tracking info on mount - will be loaded lazily
    }

    public function hasOnlyDigitalProducts()
    {
        return $this->order->items->every(fn($item) => $item->product->is_product_digital ?? false);
    }

    public function getStatusInfo()
    {
        $status = OrderStatusService::getOrderStatus(
            $this->order->payment_status,
            $this->order->is_approved,
            $this->order->shipping_status
        );

        return OrderStatusService::getStatusInfo(
            $status,
            $this->paymentDeadline,
            $this->order->updated_at
        );
    }

    /**
     * Check if the order is delivered/completed
     */
    public function isOrderDelivered(): bool
    {
        return $this->order->status === 'completed';
    }

    /**
     * Submit a review for an order item
     */
    public function submitReview($orderItemId)
    {
        $rating = $this->reviewRatings[$orderItemId] ?? null;
        $reviewText = $this->reviewTexts[$orderItemId] ?? null;

        $this->validate([
            "reviewRatings.{$orderItemId}" => 'required|integer|min:1|max:5',
            "reviewTexts.{$orderItemId}" => 'nullable|string|max:1000',
            "reviewImages.{$orderItemId}" => 'nullable|array|max:5',
            "reviewImages.{$orderItemId}.*" => 'image|max:2048',
        ], [
            "reviewRatings.{$orderItemId}.required" => 'Rating harus diisi.',
            "reviewRatings.{$orderItemId}.min" => 'Rating minimal 1.',
            "reviewRatings.{$orderItemId}.max" => 'Rating maksimal 5.',
            "reviewImages.{$orderItemId}.max" => 'Maksimal 5 gambar.',
            "reviewImages.{$orderItemId}.*.image" => 'File harus berupa gambar.',
            "reviewImages.{$orderItemId}.*.max" => 'Ukuran gambar maksimal 2MB.',
        ]);

        $item = $this->order->items()->findOrFail($orderItemId);

        // Upload images
        $imagePaths = [];
        if (!empty($this->reviewImages[$orderItemId])) {
            foreach ($this->reviewImages[$orderItemId] as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        // Check if review already exists
        $existingReview = Review::where('order_item_id', $orderItemId)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            $updateData = [
                'rating' => $rating,
                'review' => $reviewText,
            ];

            // Merge new images with existing ones
            if (!empty($imagePaths)) {
                $existingImages = $existingReview->images ?? [];
                $updateData['images'] = array_merge($existingImages, $imagePaths);
            }

            $existingReview->update($updateData);
            session()->flash('message', 'Review berhasil diperbarui!');
        } else {
            Review::create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'user_id' => auth()->id(),
                'order_id' => $this->order->id,
                'order_item_id' => $orderItemId,
                'rating' => $rating,
                'review' => $reviewText,
                'images' => !empty($imagePaths) ? $imagePaths : null,
            ]);
            session()->flash('message', 'Review berhasil dikirim!');
        }

        // Reset uploaded images
        unset($this->reviewImages[$orderItemId]);

        $this->editingReviewItemId = null;

        // Refresh order with reviews
        $this->order = $this->order->fresh(['items', 'items.product', 'items.review']);
    }

    /**
     * Remove a specific image from an existing review
     */
    public function removeReviewImage($orderItemId, $imageIndex)
    {
        $review = Review::where('order_item_id', $orderItemId)
            ->where('user_id', auth()->id())
            ->first();

        if ($review && $review->images) {
            $images = $review->images;
            if (isset($images[$imageIndex])) {
                // Delete file from storage
                \Storage::disk('public')->delete($images[$imageIndex]);
                array_splice($images, $imageIndex, 1);
                $review->update(['images' => !empty($images) ? $images : null]);
                $this->order = $this->order->fresh(['items', 'items.product', 'items.review']);
            }
        }
    }

    /**
     * Start editing a review
     */
    public function editReview($orderItemId)
    {
        $item = $this->order->items()->with('review')->findOrFail($orderItemId);
        if ($item->review) {
            $this->reviewRatings[$orderItemId] = $item->review->rating;
            $this->reviewTexts[$orderItemId] = $item->review->review;
        }
        $this->editingReviewItemId = $orderItemId;
    }

    /**
     * Cancel editing a review
     */
    public function cancelEditReview()
    {
        $this->editingReviewItemId = null;
        $this->reviewImages = [];
    }

    public function render()
    {
        $statusInfo = $this->getStatusInfo();

        $this->checkPaymentStatus();

        return view('livewire.order-detail', [
            'statusInfo' => $statusInfo,
            'order' => $this->order
        ])->layout('components.layouts.app', [
            'seoTitle' => 'Detail Pesanan #' . $this->order->order_number,
            'seoDescription' => 'Detail pesanan #' . $this->order->order_number,
            'seoRobots' => 'noindex, nofollow',
        ]);
    }

    public function checkPaymentStatus()
    {
        // Skip check if order is already completed or cancelled
        if (in_array($this->order->status, ['completed', 'cancelled'])) {
            return;
        }

        if ($this->order && $this->order->payment_gateway_transaction_id) {
            try {
                $midtrans = app(MidtransService::class);
                $status = $midtrans->getStatus($this->order);

                $this->order->update([
                    'payment_gateway_data' => json_encode($status['data'])
                ]);

                if ($status['success']) {
                    switch ($status['data']->transaction_status) {
                        case 'settlement':
                            // Only update if not already paid
                            if ($this->order->payment_status !== 'paid') {
                                $this->order->update([
                                    'payment_status' => 'paid',
                                    'status' => 'awaiting_confirmation'
                                ]);
                            }
                            break;
                        case 'deny':
                        case 'cancel':
                        case 'expire':
                            $this->order->update([
                                'status' => 'cancelled'
                            ]);
                            break;
                    }
                }
            } catch (\Exception $e) {
                report($e);
            }
        }
    }

    /**
     * Update shipping status from Biteship API if order has shipping_order_id
     */
    public function updateShippingStatusIfNeeded(): void
    {
        $this->order->updateShippingStatusIfNeeded();

        // Refresh the order model to get updated data
        $this->order = $this->order->fresh();
    }



    /**
     * Manual refresh shipping status for customer
     */
    public function refreshShippingStatus()
    {
        try {
            $this->order->updateShippingStatus();

            // Refresh the order model to get updated data
            $this->order = $this->order->fresh();

            session()->flash('message', 'Status pengiriman berhasil diperbarui!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Get shipping history from shipping_order_data
     */
    public function getShippingHistory()
    {
        // Prioritas: gunakan data dari trackingInfo jika ada
        if ($this->trackingInfo && isset($this->trackingInfo['history'])) {
            return $this->trackingInfo['history'];
        }

        // Fallback ke shipping_order_data
        if (!$this->order->shipping_order_data) {
            return [];
        }

        $shippingData = $this->order->shipping_order_data;
        if (is_string($shippingData)) {
            $shippingData = json_decode($shippingData, true);
        }

        return $shippingData['history'] ?? [];
    }

    /**
     * Load tracking info from Biteship API
     */
    public function loadTrackingInfo()
    {
        // Mark as loading
        $this->isLoadingTracking = true;

        if (!$this->order->shipping_tracking_number) {
            $this->isLoadingTracking = false;
            return;
        }

        // Get courier code from shipping method detail
        $shippingDetail = $this->order->shipping_method_detail;
        if (is_string($shippingDetail)) {
            $shippingDetail = json_decode($shippingDetail, true);
        }

        $courierCode = $shippingDetail['courier_code'] ?? null;
        if (!$courierCode) {
            $this->isLoadingTracking = false;
            return;
        }

        try {
            $store = Store::first();
            if (!$store || !$store->shipping_api_key) {
                $this->isLoadingTracking = false;
                return;
            }

            $biteshipService = new BiteshipService($store->shipping_api_key);
            $this->trackingInfo = $biteshipService->getTracking(
                $this->order->shipping_tracking_number,
                $courierCode
            );

            // Auto-update order status to completed if package is delivered
            if ($this->trackingInfo && isset($this->trackingInfo['status']) && $this->trackingInfo['status'] === 'delivered') {
                if ($this->order->status !== 'completed') {
                    $this->order->update([
                        'status' => 'completed',
                        'shipping_status' => 'delivered'
                    ]);

                    // Refresh order model
                    $this->order = $this->order->fresh();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to load tracking info', [
                'order_number' => $this->order->order_number,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->isLoadingTracking = false;
        }
    }

    /**
     * Refresh tracking information
     */
    public function refreshTracking()
    {
        $this->isLoadingTracking = true;
        $this->trackingInfo = null;

        $this->loadTrackingInfo();

        $this->isLoadingTracking = false;

        if ($this->trackingInfo) {
            session()->flash('message', 'Informasi pelacakan berhasil diperbarui!');
        } else {
            session()->flash('error', 'Gagal memuat informasi pelacakan. Silakan coba lagi nanti.');
        }
    }
}
