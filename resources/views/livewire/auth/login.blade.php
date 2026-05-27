<div class="w-full min-h-screen bg-gray-50 flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="p-8">
            <!-- Logo & Welcome Text -->
            <div class="mb-8 text-center">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-primary to-secondary rounded-2xl shadow-lg">
                    <img src="/image/store.png" alt="Logo" class="w-12 h-12 brightness-0 invert">
                </div>
                <h1 class="mb-2 text-3xl font-bold text-gray-800">Selamat Datang</h1>
                <p class="text-gray-500">Silakan login untuk melanjutkan</p>
            </div>

            <!-- Login Form -->
            <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="text-gray-400 bi bi-envelope"></i>
                    </div>
                    <input wire:model.lazy="email" type="email" placeholder="masukan email anda"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                </div>
                @error('email')
                    <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                        <i class="bi bi-exclamation-circle"></i>
                        {{$message}}
                    </span>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-semibold text-gray-700">Password</label>
                    <a href="{{route('password.request')}}" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors">
                        Lupa Password?
                    </a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="text-gray-400 bi bi-lock"></i>
                    </div>
                    <input wire:model.lazy="password" type="{{ $showPassword ? 'text' : 'password' }}" id="password"
                        class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                        placeholder="masukan password">

                    <button type="button" wire:click="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors">
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

            <button type="submit"
                class="w-full py-3.5 font-semibold text-white transition-all bg-primary rounded-xl hover:bg-primary/90 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30">
                <span class="flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login
                </span>
            </button>

            <p class="text-sm text-center text-gray-600">
                Belum punya akun?
                <a href="{{route('register')}}" class="font-semibold text-primary hover:text-primary/80 transition-colors">Daftar sekarang</a>
            </p>
        </form>

        <!-- Demo Credentials Info -->
        @if(config('app.demo'))
        <div class="p-4 mt-6 border border-blue-200 bg-blue-50 rounded-xl">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="text-blue-600 bi bi-info-circle-fill"></i>
                </div>
                <div class="flex-1">
                    <h3 class="mb-2 text-sm font-semibold text-blue-800">Demo Credentials</h3>
                    <div class="space-y-1 text-xs text-blue-700">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-envelope"></i>
                            <span>admin@kanaldagang.com</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-key"></i>
                            <span>password</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>