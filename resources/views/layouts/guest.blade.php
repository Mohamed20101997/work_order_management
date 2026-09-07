<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}" dir="{{ config('app.locale_direction.' . App::getLocale(), 'ltr') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <title>@yield('title', 'Login') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-900 px-4">
    <div class="w-full max-w-md">
        <div class="flex justify-end mb-4">
            @include('partials.language-switcher')
        </div>
        <h1 class="mb-6 text-center text-xl font-semibold text-white">{{ config('app.name') }}</h1>
        <div class="rounded-xl bg-white p-6 shadow-lg">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-blue-100 px-4 py-3 text-sm text-blue-800">{{ session('status') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</body>
</html>
