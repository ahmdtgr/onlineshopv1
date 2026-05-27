<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-20 md:pb-0">
    <!-- Header Mobile Only -->
    <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
        <div class="flex items-center h-16 px-4">
            <h1 class="text-lg font-semibold">Pesanan Saya</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 md:pt-8 px-4 md:px-8 lg:px-12 md:py-8">
        <!-- Desktop Title & Description -->
        <div class="hidden md:block mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Transaksi</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan lacak semua pesanan Anda</p>
        </div>
        
        <!-- Tabs Filter -->
        <div class="hidden md:flex items-center gap-8 border-b border-gray-200 mb-6 overflow-x-auto">
            <button wire:click="setStatusFilter('all')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'all' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Semua</button>
            <button wire:click="setStatusFilter('pending')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'pending' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Belum Bayar</button>
            <button wire:click="setStatusFilter('awaiting_confirmation')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'awaiting_confirmation' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Menunggu Konfirmasi</button>
            <button wire:click="setStatusFilter('processing')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'processing' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Diproses</button>
            <button wire:click="setStatusFilter('shipped')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'shipped' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Dalam Pengiriman</button>
            <button wire:click="setStatusFilter('completed')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'completed' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Terkirim</button>
            <button wire:click="setStatusFilter('cancelled')" class="pb-3 text-sm font-medium whitespace-nowrap {{ $statusFilter === 'cancelled' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-800' }}">Dibatalkan</button>
        </div>
        
        <!-- Search Box -->
        <div class="hidden md:block mb-6">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Cari transaksimu di sini" class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <div class="space-y-4 md:space-y-4">
        @if(session()->has('message'))
            <div class="col-span-full relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @forelse($orders as $order)
            <div class="overflow-hidden border border-gray-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="md:flex md:items-start md:gap-6 md:p-6">
                    <!-- Left: Order Info -->
                    <div class="flex-1">
                        <!-- Order Header -->
                        <div class="p-4 md:p-0 md:mb-4 border-b md:border-b-0 border-gray-100 bg-gray-50 md:bg-transparent">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm text-gray-600">
                                    {{ $order->created_at->format('d M Y') }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="{{ $this->getStatusClass($order->shipping_status) }} text-xs md:text-sm font-medium">
                                        @php
                                            $simplified = \App\Services\ShippingStatusService::getSimplifiedStatus($order->shipping_status);
                                        @endphp
                                        {{ $statusLabels[$simplified] ?? 'Belum Diproses' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-800">
                                {{$order->order_number}}
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="p-4 md:p-0">
                            @foreach($order->items->take(2) as $item)
                                <div class="flex gap-3 {{ !$loop->first ? 'mt-3 pt-3 border-t border-gray-100' : '' }}">
                                    <img src="{{ $item->product->first_image_url ?? asset('image/no-pictures.png') }}"
                                        alt="{{ $item->product_name }}" class="object-cover w-16 h-16 md:w-12 md:h-12 rounded-lg flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium line-clamp-1">{{ $item->product_name }}</h3>
                                        @if($item->product_variant_id)
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if($item->variant_type1 && $item->variant_option1)
                                                    {{ $item->variant_type1 }}: {{ $item->variant_option1 }}
                                                @endif
                                                @if($item->variant_type2 && $item->variant_option2)
                                                    , {{ $item->variant_type2 }}: {{ $item->variant_option2 }}
                                                @endif
                                            </p>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-600">
                                            {{ $item->quantity }} barang x Rp{{ number_format($item->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($order->items->count() > 2)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-500">dan {{ $order->items->count() - 2 }} barang lainnya</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right: Total & Actions -->
                    <div class="p-4 md:p-0 border-t md:border-t-0 md:border-l border-gray-100 md:pl-6 md:min-w-[280px] flex flex-col justify-between">
                        <!-- Payment Status -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                @if($order->payment_status === 'paid')
                                    <i class="mr-1 bi bi-check-circle-fill"></i> Dibayar
                                @else
                                    <i class="mr-1 bi bi-clock-fill"></i> Belum Bayar
                                @endif
                            </span>
                        </div>
                        
                        <!-- Total -->
                        <div class="mb-4">
                            <div class="text-xs text-gray-500 mb-1">Total:</div>
                            <div class="text-lg font-bold text-gray-800">
                                Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('order-detail', ['orderNumber' => $order->order_number]) }}"
                                class="px-4 py-2 text-sm text-center border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors">
                                Lihat Detail Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center min-h-[60vh] bg-white rounded-xl border border-gray-200 p-8">
                <!-- Icon pesanan kosong -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p class="mb-2 text-xl font-medium text-gray-400">Belum Ada Pesanan</p>
                <p class="text-sm text-gray-400">Anda belum melakukan pemesanan apapun</p>

                <!-- Tombol Mulai Belanja -->
                <a href="{{ route('home') }}"
                    class="px-6 py-2 mt-6 text-sm text-white transition-colors rounded-full bg-primary hover:bg-primary/90">
                    Mulai Belanja
                </a>
            </div>
        @endforelse
        </div>
    </div>
</div>