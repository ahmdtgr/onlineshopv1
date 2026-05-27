<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-32 md:pb-0">
    <!-- Header - Only Mobile -->
    <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
        <div class="flex items-center h-16 px-4">
            <button onclick="history.back()" class="p-2 rounded-full hover:bg-gray-50">
                <i class="text-xl bi bi-arrow-left"></i>
            </button>
            <h1 class="ml-2 text-lg font-semibold">Keranjang</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-16 md:pt-8 md:px-8 lg:px-12 md:py-8">
        <!-- Desktop Title & Breadcrumb -->
        <div class="hidden md:block mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <i class="bi bi-chevron-right text-xs"></i>
                <span class="text-gray-800 font-medium">Keranjang</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Keranjang Belanja</h1>
        </div>

        <div class="md:grid md:grid-cols-[1fr_400px] md:gap-8 md:items-start">
            <div class="space-y-4">
                @forelse($carts as $cart)
                    <!-- Cart Item Card -->
                    <div class="p-4 bg-white border border-gray-200 shadow-sm md:rounded-xl">
                        <div class="flex gap-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img src="{{$cart->product->first_image_url ?? asset('image/no-pictures.png')}}"
                                    alt="{{$cart->product->name}}"
                                    class="object-cover w-24 h-24 border border-gray-100 rounded-xl">
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="mb-1 text-base font-semibold text-gray-800 line-clamp-2">{{$cart->product->name}}
                                </h3>

                                @if($cart->variant)
                                    <div class="mb-2">
                                        <span class="text-sm text-gray-500">Ukuran: {{$cart->variant->variant_name}}</span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between mt-3">
                                    <div class="text-lg font-bold text-primary">
                                        Rp
                                        {{number_format($cart->variant ? $cart->variant->price : $cart->product->price, 0, ',', '.')}}
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center overflow-hidden border-2 border-gray-200 rounded-lg">
                                        <button wire:click="decrementQuantity({{$cart->id}})"
                                            class="flex items-center justify-center text-gray-600 transition-colors w-9 h-9 hover:bg-gray-50 hover:text-primary">
                                            <i class="text-lg bi bi-dash"></i>
                                        </button>
                                        <input type="text" readonly value="{{$cart->quantity}}"
                                            class="w-12 text-sm font-semibold text-center bg-white border-gray-200 border-x-2">
                                        <button wire:click="incrementQuantity({{$cart->id}})"
                                            class="flex items-center justify-center text-gray-600 transition-colors w-9 h-9 hover:bg-gray-50 hover:text-primary">
                                            <i class="text-lg bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div
                        class="bg-white rounded-2xl p-12 flex flex-col items-center justify-center min-h-[60vh] mx-4 md:mx-0">
                        <div class="flex items-center justify-center w-32 h-32 mb-6 bg-gray-100 rounded-full">
                            <i class="text-6xl text-gray-300 bi bi-cart3"></i>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-800">Keranjang Kosong</h3>
                        <p class="mb-6 text-center text-gray-500">Belum ada produk dalam keranjang belanja Anda</p>

                        <a href="{{ route('home') }}"
                            class="px-8 py-3 font-semibold text-white transition-all shadow-lg bg-primary rounded-xl hover:bg-primary/90 shadow-primary/30 hover:shadow-xl hover:shadow-primary/40">
                            <i class="mr-2 bi bi-arrow-left"></i>
                            Mulai Belanja
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Summary Column -->
            @if($carts->isNotEmpty())
                <div class="sticky hidden md:block top-24">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-800">Ringkasan Pesanan</h3>
                        </div>
                        
                        <div class="p-5">
                            <div class="space-y-3 pb-5 mb-5 border-b border-gray-100">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-semibold text-gray-800">Rp {{number_format($total)}}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Total Item</span>
                                    <span class="text-gray-600">{{$totalItems}} item</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mb-5">
                                <span class="font-semibold text-gray-900">Total Pembayaran</span>
                                <span class="text-xl font-bold text-primary">Rp {{number_format($total)}}</span>
                            </div>

                            <button wire:click="checkout"
                                class="flex items-center justify-center w-full h-12 gap-2 font-semibold text-white transition-all shadow-lg rounded-xl bg-primary hover:bg-primary/90 shadow-primary/20">
                                <i class="bi bi-bag-check"></i>
                                <span>Checkout</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($carts->isNotEmpty())
        <!-- Mobile Bottom Bar -->
        <div
            class="fixed bottom-[70px] left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-200 p-4 z-50 md:hidden shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs text-gray-500">Total Harga</p>
                    <p class="text-xl font-bold text-primary">Rp {{number_format($total)}}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">{{$totalItems}} Barang</p>
                </div>
            </div>

            <button wire:click="checkout"
                class="flex items-center justify-center w-full h-12 font-bold text-white transition-colors shadow-lg rounded-xl bg-primary hover:bg-primary/90 shadow-primary/30">
                Checkout
            </button>
        </div>
    @endif
</div>