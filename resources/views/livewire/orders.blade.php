<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-20 md:pb-0">
    <!-- Header Mobile Only -->
    <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
        <div class="flex items-center h-16 px-4">
            <h1 class="text-lg font-semibold">Pesanan Saya</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-16 md:pt-8 px-4 md:px-8 lg:px-12 md:py-8">
        <!-- Desktop Title -->
        <h1 class="hidden mb-6 text-2xl font-bold text-gray-800 md:block">Pesanan Saya</h1>
        
        <div class="space-y-4 md:space-y-6 md:grid md:grid-cols-2 xl:grid-cols-3 md:gap-6">
        @if(session()->has('message'))
            <div class="col-span-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @forelse($orders as $order)
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow h-fit">
                <!-- Order Header -->
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-gray-400">
                            {{$order->order_number}}
                        </div>
                        <span class="{{ $this->getStatusClass($order->shipping_status) }} font-medium">
                            @php
                                $simplified = \App\Services\ShippingStatusService::getSimplifiedStatus($order->shipping_status);
                            @endphp
                            {{ $statusLabels[$simplified] ?? 'Belum Diproses' }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $order->created_at->format('d M Y H:i') }}
                    </div>
                </div>

                <!-- Order Items -->
                @foreach($order->items as $item)
                    <div class="p-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex gap-3">
                            <img src="{{ $item->product->first_image_url ?? asset('image/no-pictures.png') }}"
                                alt="{{ $item->product_name }}" class="w-20 h-20 object-cover rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium">{{ $item->product_name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $item->variant_name ?? 'Default' }}
                                </p>
                                <div class="mt-2">
                                    <span class="text-sm text-gray-600">{{ $item->quantity }} x </span>
                                    <span class="text-sm font-medium">Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Order Total -->
                <div class="px-4 py-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Belanja</span>
                        <span class="text-primary font-semibold">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="p-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('order-detail', ['orderNumber' => $order->order_number]) }}"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-full text-gray-600 hover:border-primary hover:text-primary">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 flex flex-col items-center justify-center min-h-[60vh]">
                <!-- Icon pesanan kosong -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p class="text-xl font-medium text-gray-400 mb-2">Belum Ada Pesanan</p>
                <p class="text-sm text-gray-400">Anda belum melakukan pemesanan apapun</p>

                <!-- Tombol Mulai Belanja -->
                <a href="{{ route('home') }}"
                    class="mt-6 px-6 py-2 bg-primary text-white rounded-full text-sm hover:bg-primary/90 transition-colors">
                    Mulai Belanja
                </a>
            </div>
        @endforelse
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>