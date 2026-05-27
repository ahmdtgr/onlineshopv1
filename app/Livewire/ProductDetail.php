<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Review;

class ProductDetail extends Component
{
    public $product;
    public $currentImageIndex = 0;
    public $cartCount = 0;

    public $hasVariants = false;
    public $variants = [];
    public $selectedVariantId = null;
    public $selectedVariant = null;
    public $variantTypes = [];
    public $selectedOptions = [];

    public $displayPrice;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();
        $this->updateCartCount();

        // Check if product has variants
        $this->hasVariants = $this->product->has_variants;

        if ($this->hasVariants) {
            // Load active variants
            $this->variants = $this->product->variants()
                ->where('is_active', true)
                ->get();

            // Set first variant as default if available
            if ($this->variants->count() > 0) {
                $firstVariant = $this->variants->first();
                $this->selectedVariant = $firstVariant;
                $this->selectedVariantId = $firstVariant->id;
                $this->displayPrice = $firstVariant->price;

                // Pre-select the first variant's options
                if (!empty($firstVariant->variant_type1) && !empty($firstVariant->variant_option1)) {
                    $this->selectedOptions[$firstVariant->variant_type1] = $firstVariant->variant_option1;
                }

                if (!empty($firstVariant->variant_type2) && !empty($firstVariant->variant_option2)) {
                    $this->selectedOptions[$firstVariant->variant_type2] = $firstVariant->variant_option2;
                }
            } else {
                // Fallback to product price if no variants available
                $this->displayPrice = $this->product->price ?? 0;
            }

            // Extract unique variant types
            $variantTypes = [];
            foreach ($this->variants as $variant) {
                if (!empty($variant->variant_type1) && !empty($variant->variant_option1)) {
                    if (!isset($variantTypes[$variant->variant_type1])) {
                        $variantTypes[$variant->variant_type1] = [];
                    }
                    if (!in_array($variant->variant_option1, $variantTypes[$variant->variant_type1])) {
                        $variantTypes[$variant->variant_type1][] = $variant->variant_option1;
                    }
                }

                if (!empty($variant->variant_type2) && !empty($variant->variant_option2)) {
                    if (!isset($variantTypes[$variant->variant_type2])) {
                        $variantTypes[$variant->variant_type2] = [];
                    }
                    if (!in_array($variant->variant_option2, $variantTypes[$variant->variant_type2])) {
                        $variantTypes[$variant->variant_type2][] = $variant->variant_option2;
                    }
                }
            }

            $this->variantTypes = $variantTypes;
        } else {
            $this->displayPrice = $this->product->price;
        }
    }

    public function updateCartCount()
    {
        $this->cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
    }


    public function addToCart($productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->hasVariants && !$this->selectedVariantId) {
            $this->dispatch('showAlert', [
                'message' => 'Silahkan pilih varian produk terlebih dahulu',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $cart = Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->where('product_variant_id', $this->selectedVariantId)
                ->first();

            if ($cart) {
                $cart->update([
                    'quantity' => $cart->quantity + 1
                ]);
            } else {
                Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
                    'product_variant_id' => $this->selectedVariantId,
                    'quantity' => 1
                ]);
            }

            $this->updateCartCount();

            $this->dispatch('showAlert', [
                'message' => 'Berhasil ditambahkan ke keranjang',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'message' => 'Gagal menambahkan ke keranjang' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function nextImage()
    {
        if ($this->currentImageIndex < count($this->product->images) - 1) {
            $this->currentImageIndex++;
        }
    }

    public function previousImage()
    {
        if ($this->currentImageIndex > 0) {
            $this->currentImageIndex--;
        }
    }

    public function render()
    {
        $reviews = Review::where('product_id', $this->product->id)
            ->with(['user', 'productVariant'])
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating');
        $reviewCount = $reviews->count();

        $productImage = $this->product->first_image_url ?? asset('image/no-pictures.png');
        $seoDescription = $this->product->description
            ? \Illuminate\Support\Str::limit(strip_tags($this->product->description), 160)
            : 'Beli ' . $this->product->name . ' dengan harga terbaik';

        $seoJsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->product->name,
            'description' => $seoDescription,
            'image' => $productImage,
            'url' => url()->current(),
            'offers' => [
                '@type' => 'Offer',
                'price' => $this->displayPrice,
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
            ],
            ...($reviewCount > 0 ? [
                'aggregateRating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => round($averageRating, 1),
                    'reviewCount' => $reviewCount,
                ],
            ] : []),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return view('livewire.product-detail', [
            'images' => $this->product->images ?? [],
            'currentImage' => $this->product->images[$this->currentImageIndex] ?? null,
            'displayPrice' => $this->displayPrice,
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'reviewCount' => $reviewCount,
        ])
            ->layout('components.layouts.app', [
                'seoTitle' => $this->product->name,
                'seoDescription' => $seoDescription,
                'seoKeywords' => $this->product->name . ', beli online, ' . ($this->product->category->name ?? ''),
                'seoImage' => $productImage,
                'seoOgType' => 'product',
                'seoJsonLd' => $seoJsonLd,
            ]);
    }

    public function selectOption($type, $option)
    {
        $this->selectedOptions[$type] = $option;

        $this->updateSelectedVariant();
    }

    public function updateSelectedVariant()
    {
        if (empty($this->selectedOptions)) {
            $this->selectedVariant = null;
            $this->selectedVariantId = null;
            $this->displayPrice = $this->product->price;
            return;
        }

        foreach ($this->variants as $variant) {
            $matches = true;

            foreach ($this->selectedOptions as $type => $option) {
                if (
                    ($variant->variant_type1 === $type && $variant->variant_option1 !== $option) ||
                    ($variant->variant_type2 === $type && $variant->variant_option2 !== $option)
                ) {
                    $matches = false;
                    break;
                }

                if (
                    ($variant->variant_type1 !== $type && $variant->variant_type2 !== $type) ||
                    ($variant->variant_type1 === $type && empty($variant->variant_option1)) ||
                    ($variant->variant_type2 === $type && empty($variant->variant_option2))
                ) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $this->selectedVariant = $variant;
                $this->selectedVariantId = $variant->id;
                $this->displayPrice = $variant->price;
                return;
            }
        }

        $this->selectedVariant = null;
        $this->selectedVariantId = null;
        $this->displayPrice = $this->product->price;
    }
}
