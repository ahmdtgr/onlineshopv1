<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-24 md:pb-0">
        <!-- Header Mobile Only -->
        <div class="fixed md:hidden top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50 shadow-sm">
            <div class="flex items-center h-16 px-4">
                <button onclick="history.back()" class="p-2 hover:bg-gray-50 rounded-full">
                    <i class="bi bi-arrow-left text-xl"></i>
                </button>
                <h1 class="ml-2 text-lg font-semibold">Profil Saya</h1>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="pt-16 md:pt-8 md:px-8 lg:px-12 md:py-8">
            <!-- Desktop Title -->
            <div class="hidden md:block mb-6">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                    <i class="bi bi-chevron-right text-xs"></i>
                    <span class="text-gray-800 font-medium">Profil</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
            </div>

            <!-- 2 Column Layout for Desktop -->
            <div class="md:grid md:grid-cols-[320px_1fr] md:gap-6 md:items-start">
                <!-- Left Column: Profile Card -->
                <div class="space-y-4">
                    <!-- Profile Header Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-br from-primary to-secondary p-6">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mb-4">
                                    <i class="bi bi-person text-4xl text-white"></i>
                                </div>
                                <div class="text-white">
                                    <h2 class="text-xl font-semibold mb-1">{{$name}}</h2>
                                    <p class="text-sm text-white/80">{{$email}}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isAdmin)
                    <!-- Admin Dashboard Button -->
                    <a href="/admin" class="w-full p-4 text-white flex items-center justify-center gap-2 bg-primary rounded-xl hover:opacity-90 transition-colors">
                        <i class="bi bi-speedometer2"></i>
                        <span class="font-medium">Dashboard Admin</span>
                    </a>
                    @endif

                    <!-- Logout Button -->
                    <button wire:click="logout" class="w-full p-4 text-red-500 flex items-center justify-center gap-2 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="font-medium">Keluar</span>
                    </button>
                </div>
                <!-- End Left Column -->

                <!-- Right Column: Account Info -->
                <div class="space-y-6 mt-6 md:mt-0">
                    <!-- Account Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-lg text-gray-800">Informasi Akun</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <i class="bi bi-person text-blue-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm text-gray-500 mb-1">Nama Lengkap</div>
                                    <div class="font-medium text-gray-800">{{$name}}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                                    <i class="bi bi-envelope text-purple-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm text-gray-500 mb-1">Email</div>
                                    <div class="font-medium text-gray-800">{{$email}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-lg text-gray-800">Bantuan & Dukungan</h3>
                        </div>
                        <div class="p-5">
                            <a href="https://wa.me/{{$whatsapp}}" target="_blank" class="flex items-center justify-between p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="bi bi-whatsapp text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800 group-hover:text-green-700">Hubungi Toko via WhatsApp</div>
                                        <div class="text-sm text-gray-500">Chat dengan kami untuk bantuan</div>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right text-gray-400 group-hover:text-green-600"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Right Column -->
            </div>
        </div>
    </div>
