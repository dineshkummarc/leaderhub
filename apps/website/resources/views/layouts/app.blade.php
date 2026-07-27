<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'LeaderHub')) - {{ config('app.name', 'LeaderHub') }}</title>
    <meta name="description" content="@yield('seo_description', 'LeaderHub is a premium fitness equipment distributor.')">
    <link rel="icon" type="image/png" href="{{ asset('logo/main-logo/icon-rdHD.png') }}">
    @if(View::hasSection('seo_image'))
    <meta property="og:image" content="@yield('seo_image')">
    @endif

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Organization",
      "name": "LeaderHub",
      "url": "{{ config('app.url') }}",
      "logo": "{{ asset('logo/main-logo/main-blHD.png') }}",
      "contactPoint": {
        "@@type": "ContactPoint",
        "telephone": "+6281234567890",
        "contactType": "customer service"
      }
    }
    </script>
</head>
<body class="bg-bg text-text antialiased selection:bg-accent selection:text-white transition-colors duration-200">
    @include('partials.header')
    
    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.inquiry-bar')
</body>
</html>
