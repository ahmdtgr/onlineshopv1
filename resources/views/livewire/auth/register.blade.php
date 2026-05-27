<div class="w-full min-h-screen bg-gray-50 flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="p-8">
            <!-- Logo & Welcome Text -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-primary to-secondary rounded-2xl mx-auto flex items-center justify-center mb-6 shadow-lg">
                    <img src="https://dewakoding.com/user/img/logo.png" alt="Logo" class="w-12 h-12 brightness-0 invert">
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Buat Akun Baru</h1>
                <p class="text-gray-500">Daftar untuk memulai berbelanja</p>
            </div>

            <!-- Register Form -->
            <form wire:submit.prevent="register" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-person"></i>
                        </div>
                        <input 
                            wire:model.lazy="name"
                            type="text" 
                            placeholder="Masukkan nama lengkap" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    </div>
                    @error('name')
                        <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                            <i class="bi bi-exclamation-circle"></i>
                            {{$message}}
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-envelope"></i>
                        </div>
                        <input type="email" 
                            wire:model.lazy="email"
                            placeholder="nama@example.com" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    </div>
                    @error('email')
                        <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                            <i class="bi bi-exclamation-circle"></i>
                            {{$message}}
                        </span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-lock"></i>
                        </div>
                        <input type="{{$showPassword ? 'text' : 'password'}}" 
                                wire:model.lazy="password"
                               placeholder="Minimal 8 karakter" 
                               class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                        <!-- Password Toggle Button -->
                        <button type="button" 
                                wire:click="togglePassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-{{ $showPassword ? 'eye-slash' : 'eye' }}"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                            <i class="bi bi-exclamation-circle"></i>
                            {{$message}}
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-lock"></i>
                        </div>
                        <input type="{{$showPassword ? 'text' : 'password'}}" 
                                wire:model.lazy="password_confirmation"
                               placeholder="Masukkan ulang password" 
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    </div>
                    @error('password_confirmation')
                        <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                            <i class="bi bi-exclamation-circle"></i>
                            {{$message}}
                        </span>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-primary text-white py-3.5 rounded-xl font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30">
                    <span wire:loading.remove wire:target="register" class="flex items-center justify-center gap-2">
                        <i class="bi bi-person-plus"></i>
                        Daftar Sekarang
                    </span>
                    <span wire:loading wire:target="register" class="hidden">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </span>
                </button>

                <p class="text-center text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{route('login')}}" class="font-semibold text-primary hover:text-primary/80 transition-colors">Login sekarang</a>
                </p>
            </form>
        </div>
    </div>
</div>
