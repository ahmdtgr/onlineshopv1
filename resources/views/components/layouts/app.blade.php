<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($seoTitle ?? '') ? $seoTitle . ' - ' . ($store->name ?? 'Toko Online') : ($store->name ?? 'Toko Online') }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $seoDescription ?? $store->description ?? 'Belanja online mudah dan aman' }}">
    <meta name="keywords" content="{{ $seoKeywords ?? ($store->name ?? 'toko online') . ', belanja online, fashion, pakaian' }}">
    <meta name="author" content="{{ $store->name ?? 'Toko Online' }}">
    <meta name="robots" content="{{ $seoRobots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $seoCanonical ?? url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seoOgType ?? 'website' }}">
    <meta property="og:url" content="{{ $seoCanonical ?? url()->current() }}">
    <meta property="og:title" content="{{ ($seoTitle ?? '') ? $seoTitle . ' - ' . ($store->name ?? 'Toko Online') : ($store->name ?? 'Toko Online') }}">
    <meta property="og:description" content="{{ $seoDescription ?? $store->description ?? 'Belanja online mudah dan aman' }}">
    <meta property="og:image" content="{{ $seoImage ?? ($store->imageUrl ?? asset('image/store.png')) }}">
    <meta property="og:site_name" content="{{ $store->name ?? 'Toko Online' }}">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $seoTwitterCard ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ ($seoTitle ?? '') ? $seoTitle . ' - ' . ($store->name ?? 'Toko Online') : ($store->name ?? 'Toko Online') }}">
    <meta name="twitter:description" content="{{ $seoDescription ?? $store->description ?? 'Belanja online mudah dan aman' }}">
    <meta name="twitter:image" content="{{ $seoImage ?? ($store->imageUrl ?? asset('image/store.png')) }}">

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <link rel="apple-touch-icon" href="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <meta name="msapplication-TileImage" content="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <meta name="theme-color" content="{{$store->primary_color ?? '#ff6666'}}">

    <!-- JSON-LD Structured Data -->
    @if(!empty($seoJsonLd))
        <script type="application/ld+json">{!! $seoJsonLd !!}</script>
    @endif

    @livewireStyles

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "{{$store->primary_color ?? '#ff6666'}}",
                        secondary: "{{$store->secondary_color ?? '#818CF8'}}",
                        accent: '#C7D2FE',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 md:pt-[70px]">

    @if(!isset($hideBottomNav) || !$hideBottomNav)
        @livewire('components.bottom-navigation')
    @elseif(isset($hideBottomNavMobile) && $hideBottomNavMobile)
        <div class="md:hidden">
            <!-- Hidden on mobile only -->
        </div>
        <div class="hidden md:block">
            @livewire('components.bottom-navigation')
        </div>
    @endif

    {{ $slot }}

    @livewire('components.alert')



    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @livewireScripts
    @livewireScriptConfig

    @stack('scripts')
</body>

</html>