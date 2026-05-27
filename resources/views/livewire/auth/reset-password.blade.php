<div class="w-full min-h-screen bg-gray-50 flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="p-8">
            <!-- Logo & Title -->
            <div class="mb-8 text-center">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-primary to-secondary rounded-2xl shadow-lg">
                    <i class="text-4xl text-white bi bi-shield-lock"></i>
                </div>
                <h1 class="mb-2 text-3xl font-bold text-gray-800">Reset Password</h1>
                <p class="text-gray-500">Masukkan password baru Anda</p>
            </div>

            <!-- Reset Password Form -->
            <form wire:submit.prevent="resetPassword" class="space-y-5">
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-envelope"></i>
                        </div>
                        <input wire:model.lazy="email" type="email" placeholder="nama@example.com" readonly
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50">
                    </div>
                    @error('email')
                        <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                            <i class="bi bi-exclamation-circle"></i>
                            {{$message}}
                        </span>
                    @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-lock"></i>
                        </div>
                        <input wire:model.lazy="password" type="{{ $showPassword ? 'text' : 'password' }}"
                            class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                            placeholder="Masukkan password baru">

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

                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 bi bi-lock-fill"></i>
                        </div>
                        <input wire:model.lazy="password_confirmation" type="{{ $showPasswordConfirmation ? 'text' : 'password' }}"
                            class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                            placeholder="Konfirmasi password baru">

                        <button type="button" wire:click="togglePasswordConfirmation"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="bi bi-{{ $showPasswordConfirmation ? 'eye-slash' : 'eye' }}"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                            <i class="bi bi-exclamation-circle"></i>
                            {{$message}}
                        </span>
                    @enderror
                </div>

                <!-- Password Requirements -->
                <div class="p-4 border border-gray-200 bg-gray-50 rounded-xl">
                    <h3 class="mb-2 text-xs font-semibold text-gray-700">Persyaratan Password:</h3>
                    <ul class="space-y-1 text-xs text-gray-600">
                        <li class="flex items-center gap-2">
                            <i class="bi bi-check-circle {{ strlen($password) >= 8 ? 'text-green-600' : 'text-gray-400' }}"></i>
                            <span>Minimal 8 karakter</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="bi bi-check-circle {{ $password === $password_confirmation && strlen($password) > 0 ? 'text-green-600' : 'text-gray-400' }}"></i>
                            <span>Password dan konfirmasi harus sama</span>
                        </li>
                    </ul>
                </div>

                <button type="submit"
                    class="w-full py-3.5 font-semibold text-white transition-all bg-primary rounded-xl hover:bg-primary/90 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30">
                    <span class="flex items-center justify-center gap-2">
                        <i class="bi bi-shield-check"></i>
                        Reset Password
                    </span>
                </button>

                <div class="text-center">
                    <a href="{{route('login')}}" class="text-sm font-semibold text-gray-600 hover:text-primary transition-colors inline-flex items-center gap-1">
                        <i class="bi bi-arrow-left"></i>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
