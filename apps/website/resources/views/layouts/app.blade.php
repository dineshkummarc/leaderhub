<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LeaderHub') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased selection:bg-primary-500 selection:text-white">
    @include('partials.header')
    
    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.inquiry-bar')
</body>
</html>
