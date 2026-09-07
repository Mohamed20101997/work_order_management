<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}" dir="{{ config('app.locale_direction.' . App::getLocale(), 'ltr') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <style>[x-cloak]{display:none!important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">
<nav class="bg-slate-900 text-white" x-data="{ open: false }">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex h-14 items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-semibold">{{ config('app.name') }}</a>
                <div class="hidden items-center gap-4 text-sm md:flex">
                    @include('partials.nav-links')
                </div>
            </div>
            <div class="flex items-center gap-3">
                @include('partials.language-switcher')
                <span class="hidden text-sm text-slate-300 md:inline">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-slate-300 hover:text-white">{{ __('Logout') }}</button>
                </form>
                <button class="text-xl md:hidden" @click="open = !open" aria-label="Menu">&#9776;</button>
            </div>
        </div>
        <div x-show="open" x-cloak class="flex flex-col gap-3 pb-4 text-sm md:hidden">
            @include('partials.nav-links')
        </div>
    </div>
</nav>
<main class="mx-auto max-w-7xl px-4 py-6">
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('status'))
        <div class="mb-4 rounded-lg bg-blue-100 px-4 py-3 text-sm text-blue-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-sm text-red-800">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
