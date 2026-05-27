<!-- Main Container -->
<div
    class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-white min-h-screen relative shadow-lg md:shadow-none pb-[70px] md:pb-0">
    <!-- Desktop: Split Layout Container -->
    <div class="md:grid md:grid-cols-[280px_1fr] lg:grid-cols-[320px_1fr] md:gap-8 md:px-8 lg:px-12 md:py-6">

        <!-- Left Sidebar (Desktop Only) -->
        <div class="hidden md:block">
            <!-- Store Profile Card with Banner Background -->
            <div class="mb-6 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
                <!-- Banner as Background -->
                <div class="relative h-32 bg-gradient-to-br from-primary to-secondary">
                    @if($store->bannerUrl)
                        <img src="{{ $store->bannerUrl }}" alt="Banner" class="object-cover w-full h-full">
                    @endif
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>

                <!-- Store Info -->
                <div class="relative px-6 pb-6 -mt-12">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 mb-4 overflow-hidden border-4 border-white shadow-lg rounded-2xl">
                            @if($store->imageUrl)
                                <img src="{{ $store->imageUrl }}" alt="Store"
                                    class="object-cover w-full h-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full text-3xl font-bold text-white bg-primary">
                                    {{ strtoupper(substr($store->name ?? 'S', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h2 class="mb-2 text-lg font-bold text-gray-800">{{ $store->name }}</h2>
                        <p class="mb-4 text-sm text-gray-500">{{ $store->description }}</p>
                        @if($store->address)
                            <div
                                class="flex items-center w-full gap-2 px-3 py-2 text-sm text-gray-500 rounded-lg bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-4 h-4 text-primary"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-left line-clamp-2">{{ $store->address }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Categories Filter -->
            <div class="sticky p-6 bg-white border border-gray-100 shadow-sm rounded-xl top-24">
                <h3 class="mb-4 font-bold text-gray-800">Kategori</h3>
                <div class="space-y-2">
                    <button wire:click="setCategory('all')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ $selectedCategory === 'all' ? 'text-primary font-medium bg-primary/5' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span>Semua Produk</span>
                        @if($selectedCategory === 'all')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2 h-2" viewBox="0 0 8 8" fill="currentColor">
                                <circle cx="4" cy="4" r="4" />
                            </svg>
                        @endif
                    </button>
                    @foreach ($categories as $category)
                        <button wire:click="setCategory('{{ $category->id }}')"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ $selectedCategory == $category->id ? 'text-primary font-medium bg-primary/5' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="text-left line-clamp-1">{{ $category->name }}</span>
                            @if($selectedCategory == $category->id)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2 h-2" viewBox="0 0 8 8" fill="currentColor">
                                    <circle cx="4" cy="4" r="4" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Content Area (Products) -->
        <div class="md:flex-1">

            <!-- Mobile Banner (Hidden on Desktop) -->
            <div class="md:hidden h-[160px] relative overflow-hidden bg-gradient-to-br from-primary to-secondary">
                @if($store->bannerUrl)
                    <img src="{{ $store->bannerUrl }}" alt="Banner" class="object-cover w-full h-full">
                @endif
                <div class="absolute inset-0 opacity-50 pattern-dots"></div>
            </div>

            <!-- Mobile Profile Section (Hidden on Desktop) -->
            <div class="relative px-5 -mt-10 md:hidden">
                <div
                    class="w-[90px] h-[90px] rounded-[20px] overflow-hidden shadow-lg transform rotate-[5deg] border-2 border-white">
                    @if($store->imageUrl)
                        <img src="{{ $store->imageUrl }}" alt="Store"
                            class="object-cover w-full h-full transform -rotate-[5deg] scale-110">
                    @else
                        <div class="flex items-center justify-center w-full h-full text-3xl font-bold text-white transform -rotate-[5deg] scale-110 bg-primary">
                            {{ strtoupper(substr($store->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h4 class="mt-3 mb-1 text-xl font-semibold text-gray-800">{{ $store->name }}</h4>
                <p class="text-sm text-gray-500">{{ $store->description }}</p>
                @if($store->address)
                    <div class="flex items-start gap-2 mt-2 text-sm text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primary mt-0.5 flex-shrink-0"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ $store->address }}</span>
                    </div>
                @endif
            </div>

            <!-- Mobile Navigation Tabs (Hidden on Desktop) -->
            <div class="mt-5 px-2.5 overflow-x-auto hide-scrollbar md:hidden">
                <div class="flex gap-2.5 pb-2.5 whitespace-nowrap">
                    <button wire:click="setCategory('all')"
                        class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedCategory === 'all' ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                        Semua
                    </button>

                    @foreach ($categories as $category)
                        <button wire:click="setCategory('{{ $category->id }}')"
                            class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedCategory == $category->id ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-3 md:p-0">
                <!-- Search Bar -->
                <div class="mb-4 md:mb-6">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute w-4 h-4 text-gray-400 transform -translate-y-1/2 left-4 top-1/2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari produk..."
                            class="w-full py-3 pl-11 pr-4 text-sm transition-all border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @if($search)
                            <button wire:click="$set('search', '')"
                                class="absolute text-gray-400 transform -translate-y-1/2 hover:text-gray-600 right-4 top-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="items-center justify-between hidden mb-6 md:flex">
                    <h3 class="text-xl font-bold text-gray-800">
                        @if($selectedCategory === 'all')
                            Semua Produk
                        @else
                            {{ $categories->find($selectedCategory)->name }}
                        @endif
                    </h3>
                    <span class="text-sm text-gray-500">{{ $products->count() }} Produk</span>
                </div>

                @if($products->isEmpty())
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center px-4 py-12">
                        <div class="flex items-center justify-center w-24 h-24 mb-4 rounded-full bg-primary/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900">Belum Ada Produk</h3>
                        <p class="text-sm text-center text-gray-500">
                            @if($selectedCategory !== 'all')
                                Belum ada produk dalam kategori ini
                            @else
                                Toko belum menambahkan produk apapun
                            @endif
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:gap-4">
                        @foreach($products as $item)
                            <div
                                class="overflow-hidden transition-transform duration-300 bg-white shadow-sm rounded-2xl hover:-translate-y-1">
                                <a href="{{ route('product.detail', ['slug' => $item->slug]) }}" wire:navigate>
                                    <div class="relative h-[180px] bg-gray-100 flex items-center justify-center">
                                        @if($item->first_image_url)
                                            <img src="{{ $item->first_image_url }}" alt="{{$item->name}}"
                                                class="object-cover w-full h-full">
                                        @else
                                            <img src="{{ asset('image/no-pictures.png') }}" alt="No image available"
                                                class="object-contain w-1/2 h-auto opacity-60">
                                        @endif

                                        @if($item->is_product_digital)
                                            <div
                                                class="absolute top-2 left-2 bg-blue-500 text-white px-2.5 py-1 rounded-lg shadow-lg flex items-center gap-1.5">
                                                <span class="text-xs font-semibold">Digital</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <h6 class="text-sm font-medium text-gray-700 line-clamp-2">{{$item->name}}</h6>
                                        <div class="flex items-center gap-1 mt-2">
                                            @if($item->has_variants)
                                                <span class="text-xs text-gray-500">mulai dari</span>
                                            @endif
                                            <span class="text-xs text-gray-500">Rp</span>
                                            <span
                                                class="font-semibold text-primary">{{ number_format($item->display_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div> <!-- Close Right Content Area -->
    </div> <!-- Close Grid Container -->
</div> <!-- Close Main Container -->