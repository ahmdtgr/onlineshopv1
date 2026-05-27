<!-- Bottom Navigation -->
<nav
    class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] md:max-w-full md:w-full md:left-0 md:translate-x-0 md:top-0 md:bottom-auto bg-white border-t md:border-t-0 md:border-b border-gray-200 h-[70px] z-50 md:shadow-md md:backdrop-blur-sm md:bg-white/95">
    <!-- Mobile Grid -->
    <div class="grid h-full grid-cols-4 md:hidden">
        <a href="{{route('home')}}" wire:click="setActiveMenu('home')"
            class="flex flex-col items-center justify-center {{ $activeMenu === 'home' ? 'text-primary' : 'text-gray-500 hover:text-primary'}}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="text-xs">Beranda</span>
        </a>
        <a href="{{route('shopping-cart')}}" wire:click="setActiveMenu('shopping-cart')"
            class="flex flex-col items-center justify-center {{ $activeMenu === 'shopping-cart' ? 'text-primary' : 'text-gray-500 hover:text-primary'}}  transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <span class="text-xs">Keranjang</span>
        </a>
        <a href="{{route('orders')}}" wire:click="setActiveMenu('orders')"
            class="flex flex-col items-center justify-center {{ $activeMenu === 'orders' ? 'text-primary' : 'text-gray-500 hover:text-primary'}} transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
            <span class="text-xs">Pesanan</span>
        </a>
        <a href="{{route('profile')}}" wire:click="setActiveMenu('profile')"
            class="flex flex-col items-center justify-center {{ $activeMenu === 'profile' ? 'text-primary' : 'text-gray-500 hover:text-primary'}} transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <span class="text-xs">Akun</span>
        </a>
    </div>

    <!-- Desktop Menu -->
    <div class="items-center justify-between hidden h-full px-8 md:flex lg:px-12 xl:px-16">
        <!-- Logo Section -->
        <a href="{{route('home')}}" class="flex items-center gap-3 group">
            @if($store->imageUrl)
                <img src="{{ $store->imageUrl }}" alt="{{ $store->name ?? config('app.name') }}"
                    class="object-cover w-10 h-10 transition-all duration-300 shadow-md rounded-xl group-hover:shadow-lg group-hover:scale-105">
            @else
                <div class="flex items-center justify-center w-10 h-10 text-lg font-bold text-white transition-all duration-300 shadow-md rounded-xl bg-primary group-hover:shadow-lg group-hover:scale-105">
                    {{ strtoupper(substr($store->name ?? 'S', 0, 1)) }}
                </div>
            @endif
            <div class="flex flex-col">
                <span class="text-xl font-bold leading-tight text-gray-800">{{ config('app.name') }}</span>
                <span class="text-xs text-gray-500">Belanja Mudah & Aman</span>
            </div>
        </a>

        <!-- Navigation Links -->
        <div class="flex items-center gap-1">
            <a href="{{route('home')}}" wire:click="setActiveMenu('home')"
                class="relative px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $activeMenu === 'home' ? 'text-primary bg-primary/10' : 'text-gray-600 hover:text-primary hover:bg-gray-50'}}">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Beranda
                </span>
                @if($activeMenu === 'home')
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-0.5 bg-primary rounded-full"></span>
                @endif
            </a>
            <a href="{{route('shopping-cart')}}" wire:click="setActiveMenu('shopping-cart')"
                class="relative px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $activeMenu === 'shopping-cart' ? 'text-primary bg-primary/10' : 'text-gray-600 hover:text-primary hover:bg-gray-50'}}">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    Keranjang
                </span>
                @if($activeMenu === 'shopping-cart')
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-0.5 bg-primary rounded-full"></span>
                @endif
            </a>
            <a href="{{route('orders')}}" wire:click="setActiveMenu('orders')"
                class="relative px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $activeMenu === 'orders' ? 'text-primary bg-primary/10' : 'text-gray-600 hover:text-primary hover:bg-gray-50'}}">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                    Pesanan
                </span>
                @if($activeMenu === 'orders')
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-0.5 bg-primary rounded-full"></span>
                @endif
            </a>
            <a href="{{route('profile')}}" wire:click="setActiveMenu('profile')"
                class="relative px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $activeMenu === 'profile' ? 'text-primary bg-primary/10' : 'text-gray-600 hover:text-primary hover:bg-gray-50'}}">
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Akun Saya
                </span>
                @if($activeMenu === 'profile')
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/2 h-0.5 bg-primary rounded-full"></span>
                @endif
            </a>
        </div>
    </div>
</nav>