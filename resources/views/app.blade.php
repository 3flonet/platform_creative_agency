<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        @php
            $settings = \App\Models\Setting::all()->pluck('value', 'key');
            $siteName = $settings->get('site_name', config('app.name', '3FLO'));
            $description = $settings->get('site_description', 'Creative Agency Digital Solution');
            $keywords = $settings->get('site_keywords', 'agency, creative, design');
            $ogImage = $settings->get('site_og_image') ? asset('storage/' . $settings->get('site_og_image')) : null;
            $favicon = $settings->get('site_favicon') ? asset('storage/' . $settings->get('site_favicon')) : asset('favicon.ico');
        @endphp

        <title inertia>{{ $siteName }}</title>
        <meta name="description" content="{{ $description }}">
        <meta name="keywords" content="{{ $keywords }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">

        <!-- Favicon -->
        <link rel="icon" href="{{ $favicon }}" type="image/x-icon">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $siteName }}">
        <meta property="og:description" content="{{ $description }}">
        @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        @endif

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="{{ $siteName }}">
        <meta property="twitter:description" content="{{ $description }}">
        @if($ogImage)
        <meta property="twitter:image" content="{{ $ogImage }}">
        @endif
        @if($settings->get('twitter_handle'))
        <meta name="twitter:site" content="{{ $settings->get('twitter_handle') }}">
        @endif

        <!-- GEO Tags -->
        @if($settings->get('geo_placename'))
        <meta name="geo.placename" content="{{ $settings->get('geo_placename') }}">
        @endif
        @if($settings->get('geo_region'))
        <meta name="geo.region" content="{{ $settings->get('geo_region') }}">
        @endif
        @if($settings->get('geo_latitude') && $settings->get('geo_longitude'))
        <meta name="ICBM" content="{{ $settings->get('geo_latitude') }}, {{ $settings->get('geo_longitude') }}">
        <meta name="geo.position" content="{{ $settings->get('geo_latitude') }};{{ $settings->get('geo_longitude') }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="{{ asset('vendor/uicons/css/uicons-solid-rounded.css') }}">

        <style>
            .text-outline-white {
                -webkit-text-stroke: 1px white;
                text-stroke: 1px white;
                color: transparent;
            }
        </style>

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
