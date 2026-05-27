<div class="flex items-center justify-center w-full min-h-screen p-4 bg-gray-50 md:p-8">
    <div class="w-full max-w-md bg-white border border-gray-100 shadow-xl rounded-2xl">
        <div class="p-8">
            @if(!$emailSent)
                <!-- Logo & Title -->
                <div class="mb-8 text-center">
                    <div
                        class="flex items-center justify-center w-20 h-20 mx-auto mb-6 shadow-lg bg-gradient-to-br from-primary to-secondary rounded-2xl">
                        <i class="text-4xl text-white bi bi-key"></i>
                    </div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-800">Lupa Password?</h1>
                    <p class="text-gray-500">Masukkan email Anda untuk menerima link reset password</p>
                </div>

                <!-- Forgot Password Form -->
                <form wire:submit.prevent="sendResetLink" class="space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="text-gray-400 bi bi-envelope"></i>
                            </div>
                            <input wire:model.lazy="email" type="email" placeholder="nama@example.com"
                                class="w-full py-3 pl-10 pr-4 transition-all border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        @error('email')
                            <span class="flex items-center gap-1 mt-2 text-xs text-red-500">
                                <i class="bi bi-exclamation-circle"></i>
                                {{$message}}
                            </span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 font-semibold text-white transition-all bg-primary rounded-xl hover:bg-primary/90 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="sendResetLink">
                            <i class="bi bi-send"></i>
                            Kirim Link Reset Password
                        </span>
                        <span wire:loading wire:target="sendResetLink">
                            <svg class="inline-block w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengirim email...
                        </span>
                    </button>
                    <div class="text-center">
                        <a href="{{route('login')}}"
                            class="inline-flex items-center gap-1 text-sm font-semibold text-gray-600 transition-colors hover:text-primary">
                            <i class="bi bi-arrow-left"></i>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            @else
                <!-- Success Message -->
                <div class="text-center">
                    <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 bg-green-100 rounded-2xl">
                        <i class="text-4xl text-green-600 bi bi-check-circle"></i>
                    </div>
                    <h1 class="mb-2 text-3xl font-bold text-gray-800">Email Terkirim!</h1>
                    <p class="mb-6 text-gray-500">Kami telah mengirim link reset password ke:</p>
                    <p class="mb-8 text-lg font-semibold text-primary">{{ $email }}</p>

                    <div class="p-4 mb-6 border border-blue-200 bg-blue-50 rounded-xl">
                        <div class="flex items-start gap-3 text-left">
                            <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg">
                                <i class="text-blue-600 bi bi-info-circle-fill"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="mb-2 text-sm font-semibold text-blue-800">Petunjuk</h3>
                                <ul class="space-y-1 text-xs text-blue-700">
                                    <li class="flex items-start gap-2">
                                        <i class="mt-0.5 bi bi-check2"></i>
                                        <span>Periksa inbox email Anda</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="mt-0.5 bi bi-check2"></i>
                                        <span>Klik link yang kami kirimkan</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="mt-0.5 bi bi-check2"></i>
                                        <span>Buat password baru Anda</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <p class="text-sm text-gray-600">Tidak menerima email?</p>
                        <button wire:click="sendResetLink" type="button"
                            class="w-full py-3 font-semibold transition-all border-2 text-primary border-primary rounded-xl hover:bg-primary hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendResetLink">
                                <i class="bi bi-arrow-clockwise"></i>
                                Kirim Ulang
                            </span>
                            <span wire:loading wire:target="sendResetLink">
                                <svg class="inline-block w-5 h-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>

                        <a href="{{route('login')}}"
                            class="block w-full py-3 font-semibold text-center text-gray-600 transition-all border-2 border-gray-300 rounded-xl hover:bg-gray-50">
                            <span class="inline-flex items-center gap-2">
                                <i class="bi bi-arrow-left"></i>
                                Kembali ke Login
                            </span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>