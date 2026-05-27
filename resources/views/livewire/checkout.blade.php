<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-white min-h-screen relative shadow-lg md:shadow-none pb-[220px] md:pb-[70px]">
    <!-- Header Mobile Only -->
    <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
        <div class="flex items-center h-16 px-4">
            <button onclick="history.back()" class="p-2 rounded-full hover:bg-gray-50">
                <i class="text-xl bi bi-arrow-left"></i>
            </button>
            <h1 class="ml-2 text-lg font-semibold">Checkout</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-16 md:pt-8 md:px-8 lg:px-12 md:py-8">
        <!-- Desktop Title & Breadcrumb -->
        <div class="hidden mb-6 md:block">
            <div class="flex items-center gap-2 mb-2 text-sm text-gray-500">
                <a href="{{ route('shopping-cart') }}" class="transition-colors hover:text-primary">Keranjang</a>
                <i class="text-xs bi bi-chevron-right"></i>
                <span class="font-medium text-gray-800">Checkout</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Checkout</h1>
        </div>

        <div class="md:grid md:grid-cols-2 md:gap-6 lg:gap-8 md:items-start">

            <!-- Left Column: Order Summary -->
            <div class="px-4 space-y-6 md:px-0">
                <!-- Order Summary -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="text-lg bi bi-cart-check text-primary"></i>
                        <h2 class="text-lg font-semibold">Ringkasan Pesanan</h2>
                    </div>
                    <div class="p-5 bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="space-y-4">
                            @foreach($carts as $cart)
                                <div class="flex gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                    <img src="{{$cart->product->first_image_url ?? asset('image/no-pictures.png')}}"
                                        alt="{{$cart->product->name}}"
                                        class="flex-shrink-0 object-cover w-16 h-16 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="mb-1 text-sm font-medium line-clamp-2">{{$cart->product->name}}</h3>

                                        @if($cart->product->is_product_digital)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-500 text-white mb-1">
                                                Digital
                                            </span>
                                        @endif

                                        @if($cart->variant)
                                            <div class="mb-1 text-xs text-gray-500">
                                                {{$cart->variant->variant_name}}
                                            </div>
                                        @endif

                                        @php
                                            $price = $cart->variant ? $cart->variant->price : $cart->product->price;
                                        @endphp

                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-gray-500">{{$cart->quantity}} x Rp {{number_format($price)}}</span>
                                            <span class="font-semibold text-primary">Rp {{number_format($price * $cart->quantity)}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Voucher in summary card (desktop only) -->
                        <div class="hidden pt-4 mt-4 border-t border-gray-200 lg:block">
                            @if($appliedVoucher)
                                <div class="flex items-center justify-between p-2.5 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg">
                                            <i class="text-green-600 bi bi-check-circle-fill"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $appliedVoucher->code }}</p>
                                            <p class="text-xs text-gray-600">Diskon: Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="removeVoucher"
                                        type="button"
                                        class="text-red-600 hover:text-red-700">
                                        <i class="text-lg bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            @else
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Kode voucher</label>
                                    <div class="flex gap-2">
                                        <input
                                            type="text"
                                            wire:model="voucherCode"
                                            class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                            placeholder="">
                                        <button
                                            wire:click="applyVoucher"
                                            type="button"
                                            class="px-4 py-2 text-sm font-medium text-white transition-colors rounded-lg bg-primary hover:bg-primary/90">
                                            Gunakan
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Price Summary -->
                        <div class="pt-4 mt-4 space-y-2 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Subtotal ({{count($carts)}} item)</span>
                                <span class="font-medium">Rp {{number_format($total)}}</span>
                            </div>
                            @if($shippingCost > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Ongkos Kirim</span>
                                    <span class="font-medium">Rp {{number_format($shippingCost)}}</span>
                                </div>
                            @endif
                            @if($voucherDiscount > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-green-600">Diskon Voucher</span>
                                    <span class="font-medium text-green-600">- Rp {{number_format($voucherDiscount)}}</span>
                                </div>
                            @endif
                            <div class="pt-3 mt-3 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">Total Pembayaran</span>
                                    <span class="text-xl font-bold text-primary">Rp {{number_format($total + $shippingCost - $voucherDiscount)}}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Button -->
                        <button
                            wire:click="createOrder"
                            wire:loading.attr="disabled"
                            class="items-center justify-center hidden w-full h-12 gap-2 mt-4 font-medium text-white transition-colors rounded-lg lg:flex bg-primary hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-bag-check" wire:loading.remove.inline-block wire:target="createOrder"></i>
                            <svg wire:loading.inline-block wire:target="createOrder" class="hidden w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove.inline-block wire:target="createOrder">Buat Pesanan</span>
                            <span wire:loading.inline-block wire:target="createOrder" class="hidden">Memproses...</span>
                        </button>
                    </div>
                </div>
        </div>
        <!-- End Left Column -->

        <!-- Right Column: Forms -->
        <div class="px-4 space-y-6 md:px-0">                <!-- Recipient Information -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="text-lg bi bi-person text-primary"></i>
                        <h2 class="text-lg font-semibold">Data Penerima</h2>
                    </div>
                    <div class="p-5 space-y-4 bg-white border border-gray-200 shadow-sm rounded-xl">
                        <!-- Name -->
                        <div>
                            <label class="text-sm text-gray-700 mb-1.5 block flex items-center gap-1">
                                <span>Nama Lengkap</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                wire:model.blur="shippingData.recipient_name"
                                class="w-full px-4 py-2.5 rounded-lg border @error('shippingData.recipient_name') border-red-300 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="">
                            @error('shippingData.recipient_name')
                                <p class="flex items-center gap-1 mt-1 text-sm text-red-600">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="text-sm text-gray-700 mb-1.5 block flex items-center gap-1">
                                <span>Nomor Telepon</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input wire:model.blur="shippingData.phone"
                                    type="tel"
                                    class="w-full px-4 py-2.5 rounded-lg border @error('shippingData.phone') border-red-300 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                    placeholder="Contoh: 08123456789">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="text-gray-400 bi bi-telephone"></i>
                                </div>
                            </div>
                            @error('shippingData.phone')
                                <p class="flex items-center gap-1 mt-1 text-sm text-red-600">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                @if(!$this->hasOnlyDigitalProducts())
                <!-- Shipping Address -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="text-lg bi bi-geo-alt text-primary"></i>
                        <h2 class="text-lg font-semibold">Alamat Pengiriman</h2>
                    </div>
                    <div class="p-5 space-y-4 bg-white border border-gray-200 shadow-sm rounded-xl"
                        x-data="{
                            open: false,
                            search: '',
                            areaName: '',
                            selectedId: @entangle('shippingData.area_id'),
                            isLoading: false,
                            isLoadingShipping: false
                        }"
                        x-init="$watch('selectedId', value => {
                            if (!value) {
                                areaName = '';
                            }
                        })">
                        <!-- Area Search -->
                        <div class="relative">
                            <label class="text-sm text-gray-700 mb-1.5 block flex items-center gap-1">
                                <span>Lokasi</span>
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">
                                <!-- Search Input -->
                                <div class="relative">
                                    <input type="text"
                                        x-model="search"
                                        x-on:focus="open = true"
                                        x-on:input.debounce.500ms="
                                            isLoading = true;
                                            $wire.searchAreas($event.target.value).then(() => {
                                                isLoading = false;
                                            });
                                        "
                                        placeholder="Masukkan nama kecamatan..."
                                        class="w-full px-4 py-2.5 pl-11 rounded-lg border @error('shippingData.area_id') border-red-300 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">

                                    <!-- Search Icon -->
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <i x-show="!isLoading" class="text-gray-400 bi bi-search"></i>
                                        <div x-show="isLoading" class="w-4 h-4 border-2 rounded-full animate-spin border-primary border-t-transparent"></div>
                                    </div>
                                </div>

                                <!-- Selected Area Display -->
                                <div x-show="selectedId && areaName" x-cloak
                                    x-transition
                                    class="flex items-center justify-between p-3 mt-2 border border-blue-200 rounded-lg bg-blue-50">
                                    <div class="flex items-center gap-2">
                                        <i class="text-blue-600 bi bi-check-circle-fill"></i>
                                        <span class="text-sm text-gray-900" x-text="areaName"></span>
                                    </div>
                                    <button type="button" @click="selectedId = ''; areaName = ''; search = ''; $wire.set('shippingData.area_id', '')"
                                        class="text-gray-400 hover:text-gray-600">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <!-- Area Suggestions Dropdown -->
                                <div x-show="open && search.length > 2" x-cloak
                                    x-transition
                                    @click.away="open = false"
                                    class="absolute z-50 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-xl max-h-60">

                                    <div class="overflow-y-auto max-h-60">
                                        <template x-if="$wire.areas.length > 0">
                                            <div class="p-2">
                                                <template x-for="area in $wire.areas" :key="area.id">
                                                    <button type="button"
                                                        @click="
                                                            selectedId = area.id;
                                                            areaName = area.name;
                                                            open = false;
                                                            search = '';
                                                            isLoadingShipping = true;
                                                            $wire.set('shippingData.area_id', area.id).then(() => {
                                                                isLoadingShipping = false;
                                                            });
                                                        "
                                                        class="w-full px-3 py-2.5 text-left transition-colors rounded-lg hover:bg-gray-50">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <div x-text="area.name" class="text-sm font-medium text-gray-900"></div>
                                                                <div x-text="`Kode Pos: ${area.postal_code}`" class="text-xs text-gray-500"></div>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="search.length > 2 && $wire.areas.length === 0 && !isLoading">
                                            <div class="py-6 text-sm text-center text-gray-500">
                                                <i class="block mb-2 text-2xl bi bi-inbox"></i>
                                                Belum ada layanan pengiriman<br>
                                                <span class="text-xs">Silakan pilih lokasi tujuan terlebih dahulu</span>
                                            </div>
                                        </template>

                                        <template x-if="isLoading">
                                            <div class="py-6 text-sm text-center text-gray-500">
                                                <div class="w-6 h-6 mx-auto mb-2 border-2 rounded-full border-primary border-t-transparent animate-spin"></div>
                                                Mencari lokasi...
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            @error('shippingData.area_id')
                                <p class="flex items-center gap-1 mt-1 text-sm text-red-600">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Shipping Service -->
                        <div>
                            <label class="text-sm text-gray-700 mb-1.5 block flex items-center gap-1">
                                <span>Pengiriman</span>
                                <span class="text-red-500">*</span>
                            </label>

                            <div x-show="isLoadingShipping" x-cloak class="py-6 text-sm text-center text-gray-500">
                                <div class="w-6 h-6 mx-auto mb-2 border-2 rounded-full border-primary border-t-transparent animate-spin"></div>
                                Memuat layanan pengiriman...
                            </div>

                            <div x-show="!isLoadingShipping">
                            @if(!empty($shippingServices))
                                <div class="space-y-2">
                                    @foreach($shippingServices as $service)
                                        @php
                                            $isSelected = $selectedService === json_encode($service);
                                        @endphp
                                        <div class="relative">
                                            <input type="radio"
                                                id="service-{{$loop->index}}"
                                                wire:model.live="selectedService"
                                                value="{{ json_encode($service) }}"
                                                class="sr-only peer">
                                            <label for="service-{{$loop->index}}"
                                                class="flex items-center justify-between p-3 border-2 rounded-lg cursor-pointer transition-all
                                                {{ $isSelected ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300' }}">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center {{ $isSelected ? 'border-primary bg-primary' : 'border-gray-300' }}">
                                                        @if($isSelected)
                                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{$service['courier_name']}} - {{$service['courier_service_name']}}</div>
                                                        <div class="text-xs text-gray-500">{{$service['duration']}}</div>
                                                    </div>
                                                </div>
                                                <div class="text-sm font-semibold text-primary">Rp {{number_format($service['price'])}}</div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-6 text-sm text-center text-gray-500 border border-gray-200 rounded-lg">
                                    <i class="block mb-2 text-2xl bi bi-inbox"></i>
                                    Belum ada layanan pengiriman<br>
                                    <span class="text-xs">Silakan pilih lokasi tujuan terlebih dahulu</span>
                                </div>
                            @endif
                            </div>

                            @error('selectedService')
                                <p class="flex items-center gap-1 mt-1 text-sm text-red-600">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Detailed Address -->
                        <div>
                            <label class="text-sm text-gray-700 mb-1.5 block flex items-center gap-1">
                                <span>Alamat</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model.blur="shippingData.shipping_address"
                                class="w-full px-4 py-2.5 rounded-lg border @error('shippingData.shipping_address') border-red-300 @else border-gray-300 @enderror focus:ring-2 focus:ring-primary focus:border-primary transition-colors resize-none"
                                rows="3"
                                placeholder="Jalan, nomor rumah, RT/RW, patokan"></textarea>
                            @error('shippingData.shipping_address')
                                <p class="flex items-center gap-1 mt-1 text-sm text-red-600">
                                    <i class="bi bi-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- Additional Notes -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="text-lg bi bi-pencil text-primary"></i>
                        <h2 class="text-lg font-semibold">Catatan Tambahan</h2>
                    </div>
                    <div class="p-5 bg-white border border-gray-200 shadow-sm rounded-xl">
                        <textarea
                            wire:model.live="shippingData.noted"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none"
                            rows="2"
                            placeholder="Catatan untuk {{ $this->hasOnlyDigitalProducts() ? 'penjual' : 'kurir' }} (opsional)"></textarea>
                    </div>
                </div>

            </div>
            <!-- End Right Column -->

        </div>
        <!-- End Grid -->

    </div>
    <!-- End Container -->

    <!-- Mobile Bottom Bar -->
    <div class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-white border-t border-gray-200 shadow-lg lg:hidden">
        <!-- Voucher Section (mobile) -->
        <div class="mb-3 lg:hidden">
            @if($appliedVoucher)
                <div class="flex items-center justify-between p-2.5 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="text-green-600 bi bi-check-circle-fill"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $appliedVoucher->code }}</p>
                            <p class="text-xs text-gray-600">Diskon: Rp {{ number_format($voucherDiscount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <button
                        wire:click="removeVoucher"
                        type="button"
                        class="text-red-600">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            @else
                <div class="flex gap-2">
                    <input
                        type="text"
                        wire:model="voucherCode"
                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg"
                        placeholder="Kode voucher">
                    <button
                        wire:click="applyVoucher"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary">
                        Gunakan
                    </button>
                </div>
            @endif
        </div>

        <!-- Summary -->
        <div class="p-3 mb-3 rounded-lg bg-gray-50">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Subtotal ({{count($carts)}} item)</span>
                <span class="font-medium">Rp {{number_format($total)}}</span>
            </div>
            @if($shippingCost > 0)
                <div class="flex items-center justify-between mt-1 text-sm">
                    <span class="text-gray-600">Ongkos Kirim</span>
                    <span class="font-medium">Rp {{number_format($shippingCost)}}</span>
                </div>
            @endif
            @if($voucherDiscount > 0)
                <div class="flex items-center justify-between mt-1 text-sm">
                    <span class="text-green-600">Diskon</span>
                    <span class="font-medium text-green-600">- Rp {{number_format($voucherDiscount)}}</span>
                </div>
            @endif
            <div class="pt-2 mt-2 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-primary">Rp {{number_format($total + $shippingCost - $voucherDiscount)}}</span>
                </div>
            </div>
        </div>

        <button
            wire:click="createOrder"
            wire:loading.attr="disabled"
            class="flex items-center justify-center w-full h-12 gap-2 font-medium text-white transition-colors rounded-lg bg-primary hover:bg-primary/90 disabled:opacity-50">
            <i class="bi bi-bag-check" wire:loading.remove wire:target="createOrder"></i>
            <svg wire:loading wire:target="createOrder" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="createOrder">Buat Pesanan</span>
            <span wire:loading wire:target="createOrder">Memproses...</span>
        </button>
    </div>

</div>

@push('scripts')
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('payment-success', (data) => {
                const snapToken = data[0].payment_gateway_transaction_id;
                const orderId = data[0].order_id;

                if (snapToken) {
                    if (snapToken.startsWith('demo-token-')) {
                        // Handler khusus mode demo
                        alert('Mode Demo: Pembayaran Virtual disimulasikan berhasil otomatis!');
                        window.location.href = `/order-detail/${orderId}`;
                        return;
                    }
                    try {
                        window.snap.pay(snapToken, {
                            onSuccess: function(result) {
                                window.location.href = `/order-detail/${orderId}`;
                            },
                            onPending: function(result) {
                                window.location.href = `/order-detail/${orderId}`;
                            },
                            onError: function(result) {
                                alert('Pembayaran gagal! Silakan coba lagi.');
                            },
                            onClose: function() {
                                alert('Anda menutup halaman pembayaran sebelum menyelesaikan transaksi');
                                window.location.href = `/`;
                            }
                        });
                    } catch (error) {
                        alert('Terjadi kesalahan saat membuka popup pembayaran');
                    }
                }
            });
        });
    </script>
@endpush
