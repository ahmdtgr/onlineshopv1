<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-[70px] md:pb-0">
        <!-- Header with Back Button - Mobile Only -->
        <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
            <div class="flex items-center justify-between h-16 px-4">
                <div class="flex items-center">
                        <button onclick="history.back()" class="p-2 hover:bg-gray-50 rounded-full">
                            <i class="bi bi-arrow-left text-xl"></i>
                        </button>
                        <h1 class="ml-2 text-lg font-semibold">Detail Produk</h1>
                </div>

                <a href="{{route('shopping-cart')}}" class="relative p-2 hover:bg-gray-50 rounded-full transition-colors">
                    <i class="bi bi-cart text-xl"></i>
                    @if($cartCount > 0)
                    <div class="absolute -top-1 -right-1 bg-primary text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-semibold shadow-sm">
                        {{$cartCount}}
                    </div>
                    @endif
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pt-16 md:pt-8 md:px-8 lg:px-12 md:py-8">
            <!-- Desktop Breadcrumb & Title -->
            <div class="hidden md:block mb-6 max-w-7xl mx-auto">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                    <i class="bi bi-chevron-right text-xs"></i>
                    <span class="text-gray-800 font-medium">{{$product->name}}</span>
                </div>
            </div>

            <div class="md:grid md:grid-cols-[1fr_1fr] lg:grid-cols-[5fr_7fr] md:gap-8 md:max-w-7xl md:mx-auto">

                <!-- Left Column: Image -->
                <div class="md:sticky md:top-24 md:h-fit">
                    <!-- Product Images Slider -->
                    <div class="relative bg-gray-100 md:bg-white md:border md:border-gray-200 h-[350px] md:h-[480px] md:rounded-xl md:overflow-hidden flex items-center justify-center md:shadow-sm">
                        @if($currentImage)
                            <img src="{{ url('storage/'. $currentImage)}}"
                                alt="{{$product->name}}"
                                class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('image/no-pictures.png') }}"
                                alt="No image available"
                                class="w-1/3 h-auto object-contain opacity-60">
                        @endif

                        @if(count($images) > 1)
                            <button wire:click="previousImage"
                                    class="absolute left-2 md:left-3 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 hover:bg-black/70 text-white transition-colors"
                                    @if($currentImageIndex == 0) disabled @endif>
                                <i class="bi bi-chevron-left text-base"></i>
                            </button>

                            <button wire:click="nextImage"
                                class="absolute right-2 md:right-3 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 hover:bg-black/70 text-white transition-colors"
                                @if($currentImageIndex == count($images) - 1) disabled @endif>
                                <i class="bi bi-chevron-right text-base"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Thumbnail Images -->
                    @if(count($images) > 1)
                    <div class="hidden md:flex gap-3 mt-4 overflow-x-auto pb-1">
                        @foreach($images as $index => $image)
                        <button
                            wire:click="$set('currentImageIndex', {{$index}})"
                            class="flex-shrink-0 w-20 h-20 rounded-lg border-2 overflow-hidden transition-all {{$currentImageIndex == $index ? 'border-primary ring-2 ring-primary/20' : 'border-gray-200 hover:border-gray-300'}}">
                            <img src="{{ url('storage/'. $image)}}"
                                alt="{{$product->name}}"
                                class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Right Column: Product Details -->
                <div class="bg-white md:bg-transparent">
                    <!-- Product Info -->
                    <div class="p-4 md:p-6 md:bg-white md:rounded-xl md:border md:border-gray-200 md:shadow-sm md:mb-4">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-3">{{$product->name}}</h2>
                        <div class="text-2xl md:text-3xl font-bold text-primary mb-4">Rp {{number_format($displayPrice, 0, ',', '.')}}</div>

                        <!-- Display stock directly for products without variants -->
                        @if(!$hasVariants)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold text-gray-600">Stok:</span>
                                <span class="text-sm font-medium text-gray-800">{{$product->stock}}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Variant Selector -->
                    @if($hasVariants && count($variantTypes) > 0)
                    <div class="p-4 md:p-6 md:bg-white md:rounded-xl md:border md:border-gray-200 md:shadow-sm md:mb-4">
                        <h3 class="text-base md:text-lg font-bold mb-4 text-gray-800">Pilih Varian</h3>

                        @foreach($variantTypes as $typeName => $options)
                        <div class="mb-3 last:mb-0">
                            <h4 class="text-xs font-semibold text-gray-600 mb-2">{{$typeName}}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($options as $option)
                                <button
                                    wire:click="selectOption('{{$typeName}}', '{{$option}}')"
                                    class="px-3 py-2 border-2 rounded-lg text-sm font-medium transition-all {{isset($selectedOptions[$typeName]) && $selectedOptions[$typeName] === $option ? 'border-primary bg-primary text-white' : 'border-gray-200 text-gray-700 hover:border-primary hover:text-primary'}}">
                                    {{$option}}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <!-- Show selected variant stock -->
                        @if($selectedVariant)
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold text-gray-600">Stok:</span>
                                <span class="text-sm font-medium text-gray-800">{{$selectedVariant->stock}}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Desktop Action Button -->
                    <div class="hidden md:block md:p-6 md:bg-white md:rounded-xl md:border md:border-gray-200 md:shadow-sm">
                        <div class="space-y-4">
                            <button
                                wire:click="addToCart({{$product->id}})"
                                {{($hasVariants && !$selectedVariantId) ? 'disabled' : ''}}
                                class="w-full h-12 flex items-center justify-center gap-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary/20">
                                <i class="bi bi-cart-plus text-lg"></i>
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description - Desktop Full Width -->
            <div class="hidden md:block md:mt-6 md:max-w-7xl md:mx-auto">
                <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Deskripsi Produk</h3>
                    <div class="text-gray-600 text-sm md:text-base leading-relaxed">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="mt-4 md:mt-6 md:max-w-7xl md:mx-auto">
                <div class="p-4 md:p-6 bg-white md:rounded-xl md:border md:border-gray-200 md:shadow-sm">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Ulasan Produk</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $reviewCount }} ulasan</p>
                        </div>
                        @if($reviewCount > 0)
                        <div class="flex items-center gap-2 bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 text-yellow-400" fill="currentColor" stroke="currentColor" stroke-width="0">
                                <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                            <span class="text-xl font-bold text-gray-800">{{ $averageRating }}</span>
                            <span class="text-sm text-gray-500">/ 5</span>
                        </div>
                        @endif
                    </div>

                    @if($reviewCount > 0)
                        {{-- Rating Summary --}}
                        <div class="mb-6 pb-6 border-b border-gray-100">
                            <div class="space-y-2">
                                @for($star = 5; $star >= 1; $star--)
                                    @php
                                        $count = $reviews->where('rating', $star)->count();
                                        $percentage = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 w-4 text-right">{{ $star }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-3.5 h-3.5 text-yellow-400" fill="currentColor">
                                            <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                        </svg>
                                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-yellow-400 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 w-6 text-right">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Review List --}}
                        <div class="space-y-5">
                            @foreach($reviews as $review)
                                <div class="pb-5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                    {{-- User & Rating --}}
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-semibold text-primary">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">{{ $review->user->name ?? 'Pengguna' }}</p>
                                                <p class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                    fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}"
                                                    stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>

                                    {{-- Variant Info --}}
                                    @if($review->productVariant)
                                        <p class="text-xs text-gray-400 mb-2">
                                            Varian: {{ $review->productVariant->variant_name }}
                                        </p>
                                    @endif

                                    {{-- Review Text --}}
                                    @if($review->review)
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $review->review }}</p>
                                    @endif

                                    {{-- Review Images --}}
                                    @if($review->images && count($review->images) > 0)
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach($review->images as $image)
                                                <img src="{{ url('storage/' . $image) }}" alt="Review image"
                                                    class="w-16 h-16 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-80 transition-opacity"
                                                    onclick="window.open(this.src, '_blank')">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                            <p class="text-sm text-gray-500">Belum ada ulasan untuk produk ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Navigation for Add to Cart & Buy -->
        <div class="fixed bottom-[70px] left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-200 p-4 z-50 md:hidden shadow-lg">
            <button
                wire:click="addToCart({{$product->id}})"
                class="w-full h-12 flex items-center justify-center gap-2 rounded-xl bg-primary text-white font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30
                    {{($hasVariants && !$selectedVariantId) ? 'opacity-75 cursor-not-allowed' : ''}}">
                <i class="bi bi-cart-plus text-lg"></i>
                Tambah ke Keranjang
            </button>
        </div>
    </div>
