@extends('layouts.app')

@section('title', $user->exists ? __('Edit User') : __('Add User'))

@section('content')
    @php($input = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500')
    <h1 class="mb-4 text-xl font-semibold">{{ $user->exists ? __("Edit {$user->name}") : __('Add User') }}</h1>

    <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="max-w-2xl space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Name') }} *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Email') }} *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Role') }} *</label>
                <select name="role" required class="{{ $input }}">
                    @foreach (\App\Models\User::ROLES as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ $user->exists ? __('New password (leave blank to keep)') : __('Password') }} *</label>
                <input type="password" name="password" @if(! $user->exists) required @endif class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" @if(! $user->exists) required @endif class="{{ $input }}">
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true))> {{ __('Active') }}
        </label>
        <div class="flex gap-3">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $user->exists ? __('Save changes') : __('Create user') }}</button>
            <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
