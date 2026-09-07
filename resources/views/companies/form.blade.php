@extends('layouts.app')

@section('title', $company->exists ? __('Edit Company') : __('Add Company'))

@section('content')
    @php($input = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500')
    <h1 class="mb-4 text-xl font-semibold">{{ $company->exists ? __("Edit {$company->name}") : __('Add Company') }}</h1>

    <form method="POST" action="{{ $company->exists ? route('companies.update', $company) : route('companies.store') }}" class="max-w-2xl space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($company->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Company name') }} *</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Contact person') }}</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" class="{{ $input }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Address') }}</label>
            <input type="text" name="address" value="{{ old('address', $company->address) }}" class="{{ $input }}">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Notes') }}</label>
            <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $company->notes) }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $company->is_active ?? true))> {{ __('Active') }}
        </label>
        <div class="flex gap-3">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $company->exists ? __('Save changes') : __('Create company') }}</button>
            <a href="{{ route('companies.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
