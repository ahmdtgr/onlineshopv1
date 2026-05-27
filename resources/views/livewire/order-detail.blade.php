<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-24 md:pb-0">
    <!-- Header Mobile Only -->
    <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
        <div class="flex items-center h-16 px-4">
            <button onclick="history.back()" class="p-2 rounded-full hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            </button>
            <h1 class="ml-2 text-lg font-semibold">Detail Pesanan</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 pt-16 md:pt-8 md:px-8 lg:px-12 md:py-8">
        <!-- Desktop Title & Breadcrumb -->
        <div class="hidden mb-6 md:block">
            <div class="flex items-center gap-2 mb-2 text-sm text-gray-500">
                <a href="{{ route('orders') }}" class="transition-colors hover:text-primary">Pesanan</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                <span class="font-medium text-gray-800">Detail Pesanan</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan</h1>
                    <p class="mt-1 text-sm text-gray-500">Order #{{$order->order_number}}</p>
                </div>
                <div class="text-right">
                    <div class="mb-1 text-sm text-gray-500">{{$order->created_at->format('d M Y, H:i')}}</div>
                    <span data-payment-status="{{$order->payment_status}}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                        @else bg-orange-100 text-orange-800
                        @endif">
                        @if($order->payment_status === 'paid')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg> Sudah Dibayar
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg> Belum Dibayar
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-green-700 border border-green-200 rounded-lg bg-green-50">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="p-4 mb-4 text-red-700 border border-red-200 rounded-lg bg-red-50">
                {{ session('error') }}
            </div>
        @endif

        <!-- Mobile-only: Status Banner (shown above everything on mobile) -->
        <div class="mt-4 mb-4 md:hidden">
            <div class="p-4 rounded-xl border
                @if($statusInfo['color'] === 'green') bg-green-50 border-green-200
                @elseif($statusInfo['color'] === 'orange') bg-orange-50 border-orange-200
                @elseif($statusInfo['color'] === 'blue') bg-blue-50 border-blue-200
                @elseif($statusInfo['color'] === 'red') bg-red-50 border-red-200
                @else bg-gray-50 border-gray-200
                @endif">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        @if($statusInfo['icon'] === 'bi-check-circle-fill')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'green') text-green-500 @elseif($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                        @elseif($statusInfo['icon'] === 'bi-clock-fill')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'orange') text-orange-500 @elseif($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg>
                        @elseif($statusInfo['icon'] === 'bi-truck')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                        @elseif($statusInfo['icon'] === 'bi-x-circle-fill')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'red') text-red-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'orange') text-orange-500 @elseif($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="font-medium
                            @if($statusInfo['color'] === 'green') text-green-600
                            @elseif($statusInfo['color'] === 'orange') text-orange-600
                            @elseif($statusInfo['color'] === 'blue') text-blue-600
                            @elseif($statusInfo['color'] === 'red') text-red-600
                            @else text-gray-600
                            @endif">{{$statusInfo['title']}}</h2>
                        <p class="text-sm
                            @if($statusInfo['color'] === 'green') text-green-600
                            @elseif($statusInfo['color'] === 'orange') text-orange-600
                            @elseif($statusInfo['color'] === 'blue') text-blue-600
                            @elseif($statusInfo['color'] === 'red') text-red-600
                            @else text-gray-600
                            @endif">{{$statusInfo['message']}}</p>
                    </div>
                </div>
            </div>

            @if($this->hasOnlyDigitalProducts())
                <div class="mt-4 overflow-hidden border border-blue-200 shadow-sm bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            </div>
                            <div>
                                <h3 class="mb-1 font-semibold text-gray-800">Produk Digital</h3>
                                <p class="text-sm text-gray-600">Pesanan Anda berisi produk digital. Tidak ada pengiriman fisik.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2 Column Layout for Desktop -->
        <div class="md:grid md:grid-cols-[1fr_380px] md:gap-6 md:items-start">
            <!-- Left Column: Order Details -->
            <div class="space-y-6">
                <!-- Order Details -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="p-4 border-b border-gray-200 md:p-5 bg-gray-50">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-medium md:text-lg">Detail Pesanan</h3>
                    <span class="font-mono text-sm text-gray-500">{{$order->order_number}}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500"> {{$order->created_at->format('d M Y H:i')}}</div>
                    <span data-payment-status="{{$order->payment_status}}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                        @if($order->payment_status === 'paid') bg-green-100 text-green-800 border border-green-200
                        @else bg-orange-100 text-orange-800 border border-orange-200
                        @endif">
                        @if($order->payment_status === 'paid')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1 inline" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                            Sudah Dibayar
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1 inline" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg>
                            Belum Dibayar
                        @endif
                    </span>
                </div>
            </div>

            <div class="p-4 md:p-5">
                @foreach($order->items as $item)
                    <div class="flex gap-3 pb-4 mb-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <img src="{{$item->product->first_image_url ?? asset('image/no-pictures.png')}}" alt="Product"
                            class="object-cover w-20 h-20 rounded-lg">
                        <div>
                            <h4 class="font-medium">{{$item->product_name}}</h4>

                            @if($item->product && $item->product->is_product_digital)
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                                    Digital
                                </span>
                            @endif

                            <!-- Tampilkan informasi varian jika tersedia -->
                            @if($item->product_variant_id)
                                <div class="mt-1">
                                    @if(isset($item->variant_type1) && isset($item->variant_option1))
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">{{ $item->variant_type1 }}:</span>
                                            {{ $item->variant_option1 }}
                                        </p>
                                    @endif

                                    @if(isset($item->variant_type2) && isset($item->variant_option2))
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">{{ $item->variant_type2 }}:</span>
                                            {{ $item->variant_option2 }}
                                        </p>
                                    @endif

                                    @if(!isset($item->variant_type1) && isset($item->variant_name))
                                        <p class="text-xs text-gray-500">{{ $item->variant_name }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-1">
                                <span class="text-sm">{{$item->quantity}} x </span>
                                <span class="font-medium">Rp {{number_format($item->price, 0, ',', '.')}}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp {{number_format($order->subtotal, 0, ',', '.')}}</span>
                    </div>
                    @if(!$this->hasOnlyDigitalProducts())
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Ongkir</span>
                        <span>Rp {{number_format($order->shipping_cost, 0, ',', '.')}}</span>
                    </div>
                    @endif
                    <div class="pt-2 border-t border-gray-200">
                        <div class="flex justify-between font-medium">
                            <span>Total</span>
                            <span class="text-primary">Rp {{number_format($order->total_amount, 0, ',', '.')}}</span>
                        </div>
                    </div>
                </div>
            </div>
                </div>

                @if($this->isOrderDelivered())
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-4 border-b border-gray-200 md:p-5 bg-gray-50">
                        <div class="flex items-center gap-2">
                            <h3 class="font-medium md:text-lg">Ulasan Produk</h3>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Berikan ulasan untuk produk yang telah Anda terima</p>
                    </div>

                    <div class="p-4 md:p-5">
                        @foreach($order->items as $item)
                            <div class="pb-5 mb-5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                {{-- Product Info --}}
                                <div class="flex gap-3 mb-4">
                                    <img src="{{ $item->product->first_image_url ?? asset('image/no-pictures.png') }}" alt="Product"
                                        class="object-cover rounded-lg w-14 h-14">
                                    <div>
                                        <h4 class="text-sm font-medium">{{ $item->product_name }}</h4>
                                        @if($item->product_variant_id)
                                            <p class="text-xs text-gray-500">
                                                @if(isset($item->variant_type1) && isset($item->variant_option1))
                                                    {{ $item->variant_type1 }}: {{ $item->variant_option1 }}
                                                @endif
                                                @if(isset($item->variant_type2) && isset($item->variant_option2))
                                                    / {{ $item->variant_type2 }}: {{ $item->variant_option2 }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($item->review && $editingReviewItemId !== $item->id)
                                    {{-- Show existing review --}}
                                    <div class="p-3 rounded-lg bg-gray-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        class="w-5 h-5 {{ $i <= $item->review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                        fill="{{ $i <= $item->review->rating ? 'currentColor' : 'none' }}"
                                                        stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                    </svg>
                                                @endfor
                                                <span class="ml-1 text-xs text-gray-500">({{ $item->review->rating }}/5)</span>
                                            </div>
                                            <button wire:click="editReview({{ $item->id }})" class="text-xs text-blue-600 hover:text-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg> Edit
                                            </button>
                                        </div>
                                        @if($item->review->review)
                                            <p class="text-sm text-gray-700">{{ $item->review->review }}</p>
                                        @endif
                                        @if($item->review->images && count($item->review->images) > 0)
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                @foreach($item->review->images as $image)
                                                    <img src="{{ url('storage/' . $image) }}" alt="Review image"
                                                        class="object-cover w-16 h-16 border border-gray-200 rounded-lg cursor-pointer"
                                                        onclick="window.open(this.src, '_blank')">
                                                @endforeach
                                            </div>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-400">{{ $item->review->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @else
                                    {{-- Review form --}}
                                    <div class="space-y-3">
                                        {{-- Star Rating --}}
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-700">Rating</label>
                                            <div x-data="{ hovered: 0, selected: {{ $reviewRatings[$item->id] ?? 0 }} }" class="flex items-center gap-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button"
                                                        @mouseenter="hovered = {{ $i }}"
                                                        @mouseleave="hovered = 0"
                                                        @click="selected = {{ $i }}; $wire.set('reviewRatings.{{ $item->id }}', {{ $i }})"
                                                        class="p-0.5 focus:outline-none transition-transform"
                                                        :class="{ 'scale-125': hovered === {{ $i }} }">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            class="w-8 h-8 transition-colors cursor-pointer"
                                                            :class="(hovered ? {{ $i }} <= hovered : {{ $i }} <= selected) ? 'text-yellow-400' : 'text-gray-300'"
                                                            :fill="(hovered ? {{ $i }} <= hovered : {{ $i }} <= selected) ? 'currentColor' : 'none'"
                                                            stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                        </svg>
                                                    </button>
                                                @endfor
                                                <span x-show="selected > 0" x-text="selected + '/5'" class="ml-2 text-sm font-medium text-gray-500"></span>
                                            </div>
                                            @error("reviewRatings.{$item->id}")
                                                <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Review Text --}}
                                        <div>
                                            <label class="block mb-1 text-sm font-medium text-gray-700">Ulasan (opsional)</label>
                                            <textarea
                                                wire:model="reviewTexts.{{ $item->id }}"
                                                rows="3"
                                                placeholder="Tulis ulasan Anda tentang produk ini..."
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                                            @error("reviewTexts.{$item->id}")
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Image Upload --}}
                                        <div>
                                            <label class="block mb-1 text-sm font-medium text-gray-700">Foto (opsional, maks 5)</label>

                                            {{-- Existing images when editing --}}
                                            @if($editingReviewItemId === $item->id && $item->review && $item->review->images)
                                                <div class="flex flex-wrap gap-2 mb-2">
                                                    @foreach($item->review->images as $imgIndex => $image)
                                                        <div class="relative group">
                                                            <img src="{{ url('storage/' . $image) }}" alt="Review image"
                                                                class="object-cover w-16 h-16 border border-gray-200 rounded-lg">
                                                            <button type="button"
                                                                wire:click="removeReviewImage({{ $item->id }}, {{ $imgIndex }})"
                                                                class="absolute flex items-center justify-center w-5 h-5 text-white transition-opacity bg-red-500 rounded-full opacity-0 -top-1 -right-1 group-hover:opacity-100 hover:bg-red-600">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Upload preview --}}
                                            @if(!empty($reviewImages[$item->id]))
                                                <div class="flex flex-wrap gap-2 mb-2">
                                                    @foreach($reviewImages[$item->id] as $tempImage)
                                                        <img src="{{ $tempImage->temporaryUrl() }}" alt="Preview"
                                                            class="object-cover w-16 h-16 border border-blue-200 rounded-lg">
                                                    @endforeach
                                                </div>
                                            @endif

                                            <label class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 transition-colors border border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-primary hover:text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" /></svg>
                                                <span>Tambah Foto</span>
                                                <input type="file" wire:model="reviewImages.{{ $item->id }}" multiple accept="image/*" class="hidden">
                                            </label>

                                            <div wire:loading wire:target="reviewImages.{{ $item->id }}" class="mt-1 text-xs text-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 inline animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M20.016 4.657v4.992" /></svg> Mengupload gambar...
                                            </div>

                                            @error("reviewImages.{$item->id}")
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                            @error("reviewImages.{$item->id}.*")
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Submit Button --}}
                                        <div class="flex items-center gap-2">
                                            <button
                                                wire:click="submitReview({{ $item->id }})"
                                                wire:loading.attr="disabled"
                                                class="px-4 py-2 text-sm font-medium text-white transition-colors rounded-lg bg-primary hover:bg-primary/90 disabled:opacity-50">
                                                <span wire:loading.remove wire:target="submitReview({{ $item->id }})">
                                                    {{ $item->review ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                                                </span>
                                                <span wire:loading wire:target="submitReview({{ $item->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 inline animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M20.016 4.657v4.992" /></svg> Mengirim...
                                                </span>
                                            </button>
                                            @if($editingReviewItemId === $item->id)
                                                <button wire:click="cancelEditReview" class="px-4 py-2 text-sm text-gray-600 transition-colors rounded-lg hover:bg-gray-100">
                                                    Batal
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <!-- End Left Column -->

            <!-- Right Column: Shipping & Payment Info -->
            <div class="mt-6 space-y-6 md:mt-0">
                <!-- Order Status Banner (desktop only, mobile version shown above grid) -->
                <div class="hidden md:block p-4 md:p-5 rounded-xl border
                    @if($statusInfo['color'] === 'green') bg-green-50 border-green-200
                    @elseif($statusInfo['color'] === 'orange') bg-orange-50 border-orange-200
                    @elseif($statusInfo['color'] === 'blue') bg-blue-50 border-blue-200
                    @elseif($statusInfo['color'] === 'red') bg-red-50 border-red-200
                    @else bg-gray-50 border-gray-200
                    @endif">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            @if($statusInfo['icon'] === 'bi-check-circle-fill')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'green') text-green-500 @elseif($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                            @elseif($statusInfo['icon'] === 'bi-clock-fill')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'orange') text-orange-500 @elseif($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg>
                            @elseif($statusInfo['icon'] === 'bi-truck')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                            @elseif($statusInfo['icon'] === 'bi-x-circle-fill')
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'red') text-red-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 @if($statusInfo['color'] === 'orange') text-orange-500 @elseif($statusInfo['color'] === 'blue') text-blue-500 @else text-gray-500 @endif" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="font-medium md:text-lg
                                @if($statusInfo['color'] === 'green') text-green-600
                                @elseif($statusInfo['color'] === 'orange') text-orange-600
                                @elseif($statusInfo['color'] === 'blue') text-blue-600
                                @elseif($statusInfo['color'] === 'red') text-red-600
                                @else text-gray-600
                                @endif">{{$statusInfo['title']}}</h2>
                            <p class="text-sm
                                @if($statusInfo['color'] === 'green') text-green-600
                                @elseif($statusInfo['color'] === 'orange') text-orange-600
                                @elseif($statusInfo['color'] === 'blue') text-blue-600
                                @elseif($statusInfo['color'] === 'red') text-red-600
                                @else text-gray-600
                                @endif">{{$statusInfo['message']}}</p>
                        </div>
                    </div>
                </div>

                @if($this->hasOnlyDigitalProducts())
                    <!-- Digital Product Notice (desktop only, mobile version shown above grid) -->
                    <div class="hidden md:block overflow-hidden border border-blue-200 shadow-sm bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                        <div class="p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                </div>
                                <div>
                                    <h3 class="mb-1 font-semibold text-gray-800">Produk Digital</h3>
                                    <p class="text-sm text-gray-600">
                                        Pesanan Anda berisi produk digital. Tidak ada pengiriman fisik.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contact/Shipping Details -->
                <div wire:key="shipping-info-{{ $order->id }}" class="mb-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="p-4 border-b border-gray-200 md:p-5 bg-gray-50 rounded-t-xl">
                        <h3 class="font-medium md:text-lg">
                            @if($this->hasOnlyDigitalProducts())
                                Informasi Pemesan
                            @else
                                Informasi Pengiriman
                            @endif
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 md:p-5">
                        <div class="flex gap-2">
                            <span class="text-gray-600 min-w-[140px]">Nama {{ $this->hasOnlyDigitalProducts() ? 'Pemesan' : 'Penerima' }}</span>
                            <span>: {{$order->recipient_name}}</span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-600 min-w-[140px]">No. Telepon</span>
                            <span>: {{$order->phone}}</span>
                        </div>

                        @if(!$this->hasOnlyDigitalProducts())
                        <div class="flex gap-2">
                            <span class="text-gray-600 min-w-[140px]">Alamat</span>
                            <span>: {{$order->shipping_address}}, {{$order->shipping_area_name}}</span>
                        </div>
                @php
                    $shippingDetail = $order->shipping_method_detail;
                    // Handle old data that might still be JSON string
                    if (is_string($shippingDetail)) {
                        $shippingDetail = json_decode($shippingDetail, true);
                    }
                @endphp
                @if($shippingDetail && is_array($shippingDetail))
                    <!-- Courier Info Card -->
                    <div class="p-3 mb-3 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{$shippingDetail['courier_name'] ?? 'N/A'}} -
                                    {{$shippingDetail['courier_service_name'] ?? $shippingDetail['service'] ?? 'N/A'}}
                                </p>
                                <p class="text-xs text-gray-500">Estimasi: {{$shippingDetail['duration'] ?? 'N/A'}}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->shipping_tracking_number)
                    <!-- No Resi - Compact Version -->
                    <div wire:key="resi-{{ $order->id }}" class="p-3 mb-3 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-xs text-gray-500">No. Resi</p>
                                <p class="text-sm font-semibold text-gray-900">{{$order->shipping_tracking_number}}</p>
                            </div>
                            <button
                                onclick="copyToClipboard('{{$order->shipping_tracking_number}}', this)"
                                class="flex items-center px-2 py-1.5 bg-white rounded border border-gray-200 text-gray-600 hover:text-blue-600 hover:border-blue-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if($order->shipping_tracking_number)
                    <!-- Shipping History Timeline Section -->
                    <div wire:key="shipping-history-{{ $order->id }}" class="mt-4">
                        <!-- Header - Always visible -->
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="flex items-center text-sm font-semibold text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Pengiriman
                            </h4>
                            <div class="flex items-center gap-2">
                                @php
                                    $shippingDetailForCode = $order->shipping_method_detail;
                                    if (is_string($shippingDetailForCode)) {
                                        $shippingDetailForCode = json_decode($shippingDetailForCode, true);
                                    }

                                    // Extract Biteship tracking URL from shipping_order_data
                                    $biteshipTrackingUrl = null;
                                    if ($order->shipping_order_data) {
                                        $shippingOrderData = $order->shipping_order_data;
                                        if (is_string($shippingOrderData)) {
                                            $shippingOrderData = json_decode($shippingOrderData, true);
                                        }
                                        if (is_array($shippingOrderData) && isset($shippingOrderData['courier']['link'])) {
                                            $biteshipTrackingUrl = $shippingOrderData['courier']['link'];
                                        }
                                    }
                                @endphp
                                @if($biteshipTrackingUrl)
                                <a
                                    href="{{ $biteshipTrackingUrl }}"
                                    target="_blank"
                                    class="flex items-center text-xs text-blue-600 hover:text-blue-700">
                                    Detail
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                                @endif
                                <button wire:click="refreshTracking" wire:loading.attr="disabled"
                                    class="flex items-center px-2 py-1 text-xs font-medium text-blue-600 transition-colors rounded bg-blue-50 hover:bg-blue-100 disabled:opacity-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        wire:loading.class="animate-spin" wire:target="refreshTracking">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Timeline Content - Lazy Loaded -->
                        <div wire:key="tracking-content-{{ $order->id }}" wire:init="loadTrackingInfo">
                            @if($isLoadingTracking)
                                <!-- Loading State with Circular Progress -->
                                <div class="flex items-center justify-center py-12">
                                    <div class="text-center">
                                        <!-- Circular Loading Animation -->
                                        <div class="relative inline-flex items-center justify-center w-16 h-16 mb-4">
                                            <!-- Outer rotating circle -->
                                            <div class="absolute w-16 h-16 border-4 border-blue-100 rounded-full"></div>
                                            <!-- Animated arc -->
                                            <div class="absolute w-16 h-16 border-4 border-transparent rounded-full border-t-blue-500 animate-spin"></div>
                                            <!-- Inner icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700">Memuat riwayat pengiriman...</p>
                                        <p class="mt-1 text-xs text-gray-500">Mohon tunggu sebentar</p>
                                    </div>
                                </div>
                            @else
                                @php
                                    $shippingHistory = $this->getShippingHistory();
                                @endphp
                                @if(!empty($shippingHistory))
                                    <!-- Current Status Badge -->
                                    @if($trackingInfo && isset($trackingInfo['status']))
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium
                                            @if($trackingInfo['status'] == 'delivered') bg-green-100 text-green-800 border border-green-200
                                            @elseif(in_array($trackingInfo['status'], ['confirmed', 'allocated', 'picking_up'])) bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @elseif(in_array($trackingInfo['status'], ['picked', 'dropping_off'])) bg-blue-100 text-blue-800 border border-blue-200
                                            @elseif(in_array($trackingInfo['status'], ['cancelled', 'returned'])) bg-red-100 text-red-800 border border-red-200
                                            @else bg-gray-100 text-gray-800 border border-gray-200
                                            @endif">
                                            @if($trackingInfo['status'] == 'delivered')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1.5 inline text-green-600" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                                            @elseif(in_array($trackingInfo['status'], ['picked', 'dropping_off']))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1.5 inline text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                                            @elseif(in_array($trackingInfo['status'], ['confirmed', 'allocated', 'picking_up']))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1.5 inline text-yellow-600" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd" /></svg>
                                            @elseif(in_array($trackingInfo['status'], ['cancelled', 'returned']))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1.5 inline text-red-600" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1.5 inline text-gray-600" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" /></svg>
                                            @endif
                                            {{ ucfirst(str_replace('_', ' ', $trackingInfo['status'])) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="relative">
                                    <!-- Timeline Line -->
                                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                                    <!-- Timeline Items -->
                                    <div class="space-y-4">
                                        @foreach(collect($shippingHistory)->sortByDesc('updated_at')->values() as $index => $history)
                                            @php
                                                $isLatest = $index === 0;
                                            @endphp
                                            <div class="relative pb-4 pl-10">
                                                <!-- Timeline Dot -->
                                                <div
                                                    class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center
                                                        @if($isLatest) bg-blue-100 border-2 border-blue-400
                                                        @else bg-gray-100 border-2 border-gray-400
                                                        @endif">
                                                    @if($isLatest)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                                                    @else
                                                        <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                                    @endif
                                                </div>

                                                <!-- Content -->
                                                <div
                                                    class="border rounded-lg p-3
                                                        @if($isLatest) bg-blue-50 border-blue-200
                                                        @else bg-gray-50 border-gray-200
                                                        @endif">
                                                    <p class="font-medium text-sm @if($isLatest) text-blue-900 @else text-gray-900 @endif">
                                                        {{ $history['note'] ?? $history['status'] ?? 'Status Update' }}
                                                    </p>
                                                    @if(isset($history['updated_at']))
                                                        <p class="text-xs mt-1 @if($isLatest) text-blue-600 @else text-gray-600 @endif">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                            {{ \Carbon\Carbon::parse($history['updated_at'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                            WIB
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if($order->payment_status === 'unpaid' && ($order->payment_gateway_transaction_id == null))
    <!-- Payment Instructions -->
    <div class="space-y-4">
        <h3 class="font-medium">Petunjuk Pembayaran</h3>

        @foreach($paymentMethods as $item)
            <!-- BCA -->
            <div class="overflow-hidden border rounded-xl">
                <div class="flex items-center gap-3 p-4 border-b bg-gray-50">
                    <img src="{{ Storage::url($item->image) }}" alt="BCA" class="h-6">
                    <span class="font-medium">{{$item->name}}</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <div class="text-sm text-gray-500">Nomor Rekening:</div>
                            <div class="font-mono text-lg font-medium">{{$item->account_number}}</div>
                            <div class="text-sm text-gray-500">a.n. {{$item->account_name}}</div>
                        </div>
                        <button class="text-primary hover:text-primary/80"
                            onclick="copyToClipboard('{{$item->account_number}}', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Important Notes -->
    <div class="p-4 mt-6 bg-blue-50 rounded-xl">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>
            <div class="text-sm text-blue-700">
                <p class="mb-1 font-medium">Penting:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Transfer sesuai dengan nominal yang tertera</li>
                    <li>Simpan bukti pembayaran</li>
                    <li>Upload bukti pembayaran setelah transfer</li>
                </ul>
            </div>
        </div>
    </div>
@endif

@if($order->payment_proof)
    <div class="p-4 mt-6 mb-6 border border-gray-200 rounded-xl">
        <h3 class="mb-4 font-medium">Bukti Pembayaran</h3>
        <div class="space-y-3">
            <img src="{{ Storage::url($order->payment_proof) }}" alt="Bukti Pembayaran"
                class="w-full border border-gray-100 rounded-lg" />

        </div>
    </div>
@endif

@if($order->payment_status === 'unpaid' && ($order->payment_gateway_transaction_id == null) && ($order->payment_proof == null))
    <!-- Desktop Payment Button -->
    <div class="hidden md:block">
        <a href="{{route('payment-confirmation', ['orderNumber' => $order->order_number])}}"
            class="block w-full py-3 font-medium text-center text-white transition-colors bg-primary rounded-xl hover:bg-primary/90">
            Konfirmasi Pembayaran
        </a>
    </div>
@endif
            </div>
            <!-- End Right Column -->



@if($order->payment_status === 'unpaid' && ($order->payment_gateway_transaction_id == null) && ($order->payment_proof == null))
    <!-- Bottom Button - Mobile Only -->
    <div class="md:hidden fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-100 p-4 z-50">
        <a href="{{route('payment-confirmation', ['orderNumber' => $order->order_number])}}"
            class="block w-full py-3 font-medium text-center text-white transition-colors bg-primary rounded-xl hover:bg-primary/90">
            Konfirmasi Pembayaran
        </a>
    </div>
@endif
        </div>
        <!-- End Grid Container -->
@push('scripts')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
        </script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const snapToken = "{{$order->payment_gateway_transaction_id}}";
            const orderId = "{{$order->order_number}}";
            const orderPaymentStatus = "{{$order->payment_status}}";
            const orderShippingStatus = "{{$order->shipping_status}}";

            // Tampilkan popup Midtrans hanya jika status masih pending
            if (snapToken && orderStatus === 'pending') {
                try {
                    window.snap.pay(snapToken, {
                        onSuccess: function (result) {
                            window.location.reload();
                        },
                        onPending: function (result) {
                            window.location.reload();
                        },
                        onError: function (result) {
                            alert('Pembayaran gagal! Silakan coba lagi.');
                        },
                        onClose: function () {
                            // Jika user menutup popup, tetap di halaman detail order
                            window.location.reload();
                        }
                    });
                } catch (error) {
                    console.error('Terjadi kesalahan saat membuka popup pembayaran:', error);
                }
            }
        });

        // Copy to clipboard function with better UX
        function copyToClipboard(text, buttonElement) {
            navigator.clipboard.writeText(text).then(function () {
                // Find the copy button and show feedback
                const copyBtn = buttonElement;
                const originalContent = copyBtn.innerHTML;

                // Change button content temporarily
                copyBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg><span class="text-sm font-medium">Tersalin!</span>';
                copyBtn.classList.remove('text-gray-600', 'hover:text-blue-600');
                copyBtn.classList.add('text-green-600', 'border-green-300');

                // Reset after 2 seconds
                setTimeout(() => {
                    copyBtn.innerHTML = originalContent;
                    copyBtn.classList.remove('text-green-600', 'border-green-300');
                    copyBtn.classList.add('text-gray-600', 'hover:text-blue-600');
                }, 2000);
            }).catch(function (error) {
                console.log('Gagal menyalin ke clipboard', error);
                alert('Gagal menyalin ke clipboard');
            });
        }
    </script>
@endpush
