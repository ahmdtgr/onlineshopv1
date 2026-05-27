<div class="w-full max-w-[480px] md:max-w-none mx-auto md:mx-0 bg-gray-50 min-h-screen relative pb-20 md:pb-8">
    <div class="px-4 pt-8 md:px-8 lg:px-12 md:py-10">
        <div class="max-w-5xl mx-auto">
            <div class="mb-8 md:mb-10">
                <div
                    class="inline-flex items-center px-3 py-1 mb-4 text-xs font-semibold tracking-wide border rounded-full border-primary/30 text-primary bg-primary/5">
                    RELEASE NOTES
                </div>

                <h1 class="text-3xl font-bold leading-tight text-gray-900 md:text-4xl">Changelog</h1>
                <p class="mt-3 text-sm leading-relaxed text-gray-600 md:text-base">
                    Semua update terbaru untuk Kanal Dagang. Halaman ini menampilkan fitur baru,
                    peningkatan performa, dan perbaikan bug di setiap rilis.
                </p>
            </div>

            <div class="space-y-5 md:space-y-6">
                @foreach($changelog as $release)
                    <article class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <div
                            class="flex flex-col gap-3 p-5 border-b border-gray-100 md:flex-row md:items-center md:justify-between md:p-6">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 md:text-2xl">{{ $release['version'] }}</h2>
                                <p class="mt-1 text-sm text-gray-500">Dirilis pada {{ $release['date'] }}</p>
                            </div>

                            <div
                                class="inline-flex items-center self-start px-3 py-1 text-xs font-medium text-gray-600 border border-gray-200 rounded-full bg-gray-50 md:self-auto">
                                {{ count($release['highlights']) }} perubahan
                            </div>
                        </div>

                        @if (isset($release['highlights']) && count($release['highlights']) > 0)
                            <ul class="p-5 space-y-3 md:p-6">
                                @foreach($release['highlights'] as $item)
                                    <li class="flex items-start gap-3.5">
                                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-primary/70 flex-shrink-0"></span>

                                        <div class="flex-1">
                                            <div
                                                class="inline-flex items-center px-2 py-0.5 mb-1.5 text-[11px] font-semibold border rounded {{ $this->badgeClass($item['type']) }}">
                                                {{ $this->badgeLabel($item['type']) }}
                                            </div>
                                            <p class="text-sm leading-relaxed text-gray-700 md:text-[15px]">{{ $item['text'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</div>
