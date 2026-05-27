<!-- FAB Beli Template - Collapsible -->
<div x-data="{ expanded: false }" class="fixed bottom-6 right-6" style="z-index: 9999;">
    <!-- Expanded State -->
    <div x-show="expanded" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-8" class="relative">
        <a href="https://dewakoding.com/tutorial/source-code-toko-online-lengkap-payment-ekspedisi-otomatis"
            target="_blank"
            class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-blue-600 rounded-full shadow-lg hover:bg-blue-700 hover:shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Beli Source Code
        </a>
        <button @click="expanded = false"
            class="absolute flex items-center justify-center w-6 h-6 text-white transition-all duration-200 bg-red-500 rounded-full shadow-md -top-2 -right-2 hover:bg-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Collapsed State - Circle Button with Animation -->
    <button x-show="!expanded" x-cloak @click="expanded = true" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-75"
        class="relative flex items-center justify-center text-white bg-blue-600 rounded-full shadow-lg w-14 h-14 hover:bg-blue-700 hover:shadow-xl fab-bounce-filament">
        <!-- Pulse Ring Animation -->
        <span class="absolute w-full h-full bg-blue-400 rounded-full opacity-75 animate-ping"></span>
        <!-- Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="relative w-6 h-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    </button>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    @keyframes fab-bounce-filament {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }

    .fab-bounce-filament {
        animation: fab-bounce-filament 1.5s ease-in-out infinite;
    }
</style>