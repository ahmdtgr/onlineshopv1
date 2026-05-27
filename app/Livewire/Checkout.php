<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Order;
use App\Models\Voucher;
use App\Services\BiteshipService;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;
use App\Services\MidtransService;

class Checkout extends Component
{
    public $carts = [];
    public $total = 0;
    public $shippingServices = [];
    public $selectedService = null;
    public $shippingCost = 0;
    public $store;
    public $areas = [];
    public $voucherCode = '';
    public $appliedVoucher = null;
    public $voucherDiscount = 0;

    protected $midtrans;

    public $shippingData = [
        'recipient_name' => '',
        'phone' => '',
        'area_id' => '',
        'shipping_address' => '',
        'noted' => ''
    ];

    protected function rules()
    {
        // Name and phone are always required, but shipping details only for physical products
        if ($this->hasOnlyDigitalProducts()) {
            return [
                'shippingData.recipient_name' => 'required|min:3|max:255',
                'shippingData.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
                'shippingData.area_id' => 'nullable',
                'shippingData.shipping_address' => 'nullable|min:10|max:500',
                'selectedService' => 'nullable'
            ];
        }

        return [
            'shippingData.recipient_name' => 'required|min:3|max:255',
            'shippingData.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'shippingData.area_id' => 'required',
            'shippingData.shipping_address' => 'required|min:10|max:500',
            'selectedService' => 'required'
        ];
    }

    protected $messages = [
        'shippingData.recipient_name.required' => 'Nama penerima wajib diisi',
        'shippingData.recipient_name.min' => 'Nama penerima minimal 3 karakter',
        'shippingData.recipient_name.max' => 'Nama penerima maksimal 255 karakter',
        'shippingData.phone.required' => 'Nomor telepon wajib diisi',
        'shippingData.phone.regex' => 'Format nomor telepon tidak valid',
        'shippingData.phone.min' => 'Nomor telepon minimal 10 digit',
        'shippingData.phone.max' => 'Nomor telepon maksimal 15 digit',
        'shippingData.area_id.required' => 'Lokasi pengiriman wajib dipilih',
        'shippingData.shipping_address.required' => 'Detail alamat wajib diisi',
        'shippingData.shipping_address.min' => 'Detail alamat minimal 10 karakter',
        'shippingData.shipping_address.max' => 'Detail alamat maksimal 500 karakter',
        'selectedService.required' => 'Layanan pengiriman wajib dipilih'
    ];

    public function boot(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function mount()
    {
        $this->loadCarts();
        if ($this->carts->isEmpty()) {
            return redirect()->route('home');
        }
        $this->store = Store::first();

        if (auth()->check()) {
            $user = auth()->user();
            $this->shippingData['recipient_name'] = $user->name;
        }
    }

    public function loadCarts()
    {
        $this->carts = Cart::where('user_id', auth()->id())
            ->with(['product', 'variant'])
            ->get();

        $this->calculateTotal();
    }

    public function searchAreas($query)
    {
        try {
            $shipping_api_key = $this->store['shipping_api_key'];
            $shipping = new BiteshipService($shipping_api_key);
            $this->areas = $shipping->searchAreas($query);
            return collect($this->areas)->map(function ($area) {
                return [
                    'id' => $area['id'],
                    'name' => "{$area['name']} ({$area['postal_code']})"
                ];
            });
        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'message' => 'Gagal memuat data area: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            return collect();
        }
    }

    public function updatedShippingDataAreaId($value)
    {
        if ($value) {
            $this->updatedSelectedService = null;
            $this->shippingCost = 0;
            $this->loadShippingRates();
        } else {
            // Reset shipping data when area is cleared
            $this->shippingServices = [];
            $this->selectedService = null;
            $this->shippingCost = 0;
        }
    }

    public function updatedSelectedService($value)
    {
        if ($value) {
            $serviceData = json_decode($value, true);
            $this->shippingCost = $serviceData['price'] ?? 0;
        }
    }

    private function getWeight()
    {
        return $this->carts
            ->filter(fn($cart) => !$cart->product->is_product_digital)
            ->sum(fn($cart) => ($cart->product->weight ?? 1000) * $cart->quantity);
    }

    private function loadShippingRates()
    {
        try {
            $store = $this->store;
            if (!$store->shipping_area_id) {
                throw new \Exception('Store location is not configured');
            }
            $shipping = new BiteshipService($store->shipping_api_key);

            // Only include non-digital products in shipping calculation
            $items = $this->carts
                ->filter(fn($cart) => !$cart->product->is_product_digital)
                ->map(function ($cart) {
                    // Get the correct price and weight based on variant
                    $price = $cart->variant ? $cart->variant->price : $cart->product->price;
                    $weight = $cart->product->weight ?? 100;
                    return [
                        'name' => $cart->product->name,
                        'description' => substr($cart->product->description ?? '', 0, 100),
                        'value' => $price,
                        'weight' => $weight,
                        'quantity' => $cart->quantity,
                        'length' => $cart->product->length ?? 10,
                        'width' => $cart->product->width ?? 10,
                        'height' => $cart->product->height ?? 10,
                    ];
                })->toArray();


            $rates = $shipping->getRates(
                $store->shipping_area_id,
                $this->shippingData['area_id'],
                $items
            );



            $this->shippingServices = $rates;

        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'message' => 'Gagal memuat biaya pengiriman: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            $this->shippingServices = [];
        }
    }
    public function hasOnlyDigitalProducts()
    {
        return $this->carts->every(fn($cart) => $cart->product->is_product_digital);
    }

    public function calculateTotal()
    {
        $this->total = 0;
        $this->totalItems = 0;

        foreach ($this->carts as $cart) {
            $price = $cart->variant ? $cart->variant->price : $cart->product->price;
            $this->total += $price * $cart->quantity;
            $this->totalItems += $cart->quantity;
        }

        // Recalculate voucher discount if voucher is applied
        if ($this->appliedVoucher) {
            $this->voucherDiscount = $this->appliedVoucher->calculateDiscount($this->total);
        }
    }

    public function applyVoucher()
    {
        $this->validate([
            'voucherCode' => 'required'
        ], [
            'voucherCode.required' => 'Kode voucher wajib diisi'
        ]);

        $voucher = Voucher::where('code', strtoupper($this->voucherCode))->first();

        if (!$voucher) {
            $this->dispatch('showAlert', [
                'message' => 'Kode voucher tidak valid',
                'type' => 'error'
            ]);
            return;
        }

        if (!$voucher->canBeUsed()) {
            $this->dispatch('showAlert', [
                'message' => 'Voucher sudah tidak berlaku atau telah habis masa berlakunya',
                'type' => 'error'
            ]);
            return;
        }

        if ($voucher->min_amount && $this->total < $voucher->min_amount) {
            $this->dispatch('showAlert', [
                'message' => 'Minimal pembelian untuk voucher ini adalah Rp ' . number_format($voucher->min_amount, 0, ',', '.'),
                'type' => 'error'
            ]);
            return;
        }

        $this->appliedVoucher = $voucher;
        $this->voucherDiscount = $voucher->calculateDiscount($this->total);

        $this->dispatch('showAlert', [
            'message' => 'Voucher berhasil digunakan! Anda mendapat diskon Rp ' . number_format($this->voucherDiscount, 0, ',', '.'),
            'type' => 'success'
        ]);
    }

    public function removeVoucher()
    {
        $this->appliedVoucher = null;
        $this->voucherDiscount = 0;
        $this->voucherCode = '';
    }

    public function render()
    {
        if ($this->carts->isEmpty()) {
            return redirect()->route('home');
        }
        return view('livewire.checkout')
            ->layout('components.layouts.app', [
                'hideBottomNavMobile' => true,
                'seoTitle' => 'Checkout',
                'seoDescription' => 'Selesaikan pembelian Anda',
                'seoRobots' => 'noindex, nofollow',
            ]);
    }

    public function validateField($field)
    {
        $this->validateOnly($field);
    }



    public function createOrder()
    {
        // Validate all required fields first
        $this->validate();

        if (!$this->carts->isEmpty()) {
            try {
                $isDigitalOnly = $this->hasOnlyDigitalProducts();
                
                if (!$isDigitalOnly && $this->selectedService != null) {
                    $serviceData = json_decode($this->selectedService, true);
                } elseif (!$isDigitalOnly && $this->selectedService == null) {
                    $this->dispatch('showAlert', [
                        'message' => 'Mohon pilih layanan pengiriman',
                        'type' => 'error'
                    ]);
                    return;
                }

                // For digital products, set default values
                if ($isDigitalOnly) {
                    $serviceData = null;
                    $selectedArea = null;
                } else {
                    $selectedArea = collect($this->areas)->firstWhere('id', $this->shippingData['area_id']);
                }

                // Calculate final amounts
                $subtotal = $this->total;
                $shippingCost = $isDigitalOnly ? 0 : $serviceData['price'];
                $voucherDiscount = $this->voucherDiscount;
                $totalAmount = $subtotal + $shippingCost - $voucherDiscount;

                $orderData = [
                    'user_id' => auth()->id(),
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                    'recipient_name' => $this->shippingData['recipient_name'],
                    'phone' => $this->shippingData['phone'],
                    'shipping_provider' => $isDigitalOnly ? null : $this->store->shipping_provider,
                    'shipping_area_id' => $isDigitalOnly ? null : $this->shippingData['area_id'],
                    'shipping_area_name' => $isDigitalOnly ? null : $selectedArea['name'],
                    'shipping_cost' => $shippingCost,
                    'shipping_method_detail' => $isDigitalOnly ? null : json_encode($serviceData),
                    'shipping_address' => $isDigitalOnly ? null : $this->shippingData['shipping_address'],
                    'noted' => $this->shippingData['noted']
                ];

                // Add voucher data if applied
                if ($this->appliedVoucher) {
                    $orderData['voucher_id'] = $this->appliedVoucher->id;
                    $orderData['voucher_code'] = $this->appliedVoucher->code;
                    $orderData['voucher_name'] = $this->appliedVoucher->name;
                    $orderData['voucher_discount'] = $voucherDiscount;
                }

                $order = Order::create($orderData);

                foreach ($this->carts as $cart) {
                    // Get the correct price based on variant
                    $price = $cart->variant ? $cart->variant->price : $cart->product->price;

                    // Create order item data
                    $orderItemData = [
                        'product_id' => $cart->product_id,
                        'product_name' => $cart->product->name,
                        'product_variant_id' => $cart->variant ? $cart->variant->id : null,
                        'quantity' => $cart->quantity,
                        'price' => $price
                    ];

                    // Tambahkan informasi varian jika ada
                    if ($cart->variant) {
                        // Simpan variant_name (gabungan semua varian)
                        $orderItemData['variant_name'] = $cart->variant->variant_name;

                        // Simpan variant type 1 dan option 1
                        if ($cart->variant->variant_type1 && $cart->variant->variant_option1) {
                            $orderItemData['variant_type1'] = $cart->variant->variant_type1;
                            $orderItemData['variant_option1'] = $cart->variant->variant_option1;
                        }

                        // Simpan variant type 2 dan option 2 jika ada
                        if ($cart->variant->variant_type2 && $cart->variant->variant_option2) {
                            $orderItemData['variant_type2'] = $cart->variant->variant_type2;
                            $orderItemData['variant_option2'] = $cart->variant->variant_option2;
                        }
                    }

                    // Buat order item
                    $order->items()->create($orderItemData);
                }

                // Note: Shipping order will be created manually from admin panel
                \Log::info('Order created, shipping order will be processed from admin panel', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);

                Cart::where('user_id', auth()->id())->delete();

                // Increment voucher usage if applied
                if ($this->appliedVoucher) {
                    $this->appliedVoucher->incrementUsage();
                }

                Notification::route('mail', $this->store->email_notification)
                    ->notify(new NewOrderNotification($order));

                if ($this->store->is_use_payment_gateway) {
                    // Load order items relation
                    $order->load('items');

                    $result = $this->midtrans->createTransaction($order, $order->items);

                    if (!$result['success']) {
                        throw new \Exception($result['message']);
                    }

                    $order->update(['payment_gateway_transaction_id' => $result['token']]);

                    $this->dispatch('payment-success', [
                        'payment_gateway_transaction_id' => $result['token'],
                        'order_id' => $order->order_number
                    ]);
                } else {
                    return redirect()->route('orders');
                }

            } catch (\Exception $e) {
                $this->dispatch('showAlert', [
                    'message' => $e->getMessage(),
                    'type' => 'error'
                ]);
            }
        } else {
            $this->dispatch('showAlert', [
                'message' => 'Keranjang belanja kosong',
                'type' => 'error'
            ]);
        }
    }
}
