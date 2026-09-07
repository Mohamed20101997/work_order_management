@extends('layouts.guest')

@section('title', __('Forgot Password'))

@section('content')
    <p class="mb-4 text-sm text-gray-600">{{ __('Enter your email and we will send you a password reset link.') }}</p>
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="mb-1 block text-sm font-medium">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('Send reset link') }}</button>
        <a href="{{ route('login') }}" class="block text-center text-sm text-blue-600 hover:underline">{{ __('Back to login') }}</a>
    </form>
@endsection
