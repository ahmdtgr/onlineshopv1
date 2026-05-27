<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
    <div class="max-w-md w-full mx-auto p-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Store Icon -->
            <div class="w-20 h-20 bg-blue-500 rounded-2xl mx-auto mb-6 flex items-center justify-center transform rotate-3">
                <svg class="w-10 h-10 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                    <path d="M24 48C10.7 48 0 58.7 0 72C0 85.3 10.7 96 24 96L69.3 96C73.2 96 76.5 98.8 77.2 102.6L129.3 388.9C135.5 423.1 165.3 448 200.1 448L456 448C469.3 448 480 437.3 480 424C480 410.7 469.3 400 456 400L200.1 400C188.5 400 178.6 391.7 176.5 380.3L171.4 352L475 352C505.8 352 532.2 330.1 537.9 299.8L568.9 133.9C572.6 114.2 557.5 96 537.4 96L124.7 96L124.3 94C119.5 67.4 96.3 48 69.2 48L24 48zM208 576C234.5 576 256 554.5 256 528C256 501.5 234.5 480 208 480C181.5 480 160 501.5 160 528C160 554.5 181.5 576 208 576zM432 576C458.5 576 480 554.5 480 528C480 501.5 458.5 480 432 480C405.5 480 384 501.5 384 528C384 554.5 405.5 576 432 576z"/>
                </svg>
            </div>

            <!-- Content -->
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Coming Soon!</h1>
            <p class="text-gray-600 mb-8">We're working hard to bring you something amazing. Stay tuned!</p>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-8">
                <div class="bg-blue-500 h-2 rounded-full w-3/4 animate-pulse"></div>
            </div>


            <!-- Register Button - Only shown if user count is 0 -->
            @if(\App\Models\User::count() === 0)
                <div class="mt-8">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Register Admin
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; {{ date('Y') }} dewakoding.com | All rights reserved.
        </p>
    </div>
</div>