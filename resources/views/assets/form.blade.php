@extends('layouts.app')

@section('title', $asset->exists ? __('Edit Asset') : __('Register Asset'))

@section('content')
    @php($input = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500')
    <h1 class="mb-4 text-xl font-semibold">{{ $asset->exists ? __("Edit {$asset->asset_number}") : __('Register Asset') }}</h1>

    <form method="POST" action="{{ $asset->exists ? route('assets.update', $asset) : route('assets.store') }}" class="max-w-3xl space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($asset->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Asset type') }} *</label>
                <select name="asset_type_id" required class="{{ $input }}">
                    <option value="">{{ __('Select type') }}</option>
                    @foreach ($assetTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('asset_type_id', $asset->asset_type_id) == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Company') }} *</label>
                <select name="company_id" required class="{{ $input }}">
                    <option value="">{{ __('Select company') }}</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('company_id', $asset->company_id) == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Asset name') }} *</label>
                <input type="text" name="name" value="{{ old('name', $asset->name) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Serial number') }}</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Manufacturer') }}</label>
                <input type="text" name="manufacturer" value="{{ old('manufacturer', $asset->manufacturer) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Model') }}</label>
                <input type="text" name="model" value="{{ old('model', $asset->model) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Location') }}</label>
                <input type="text" name="location" value="{{ old('location', $asset->location) }}" class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Priority') }} *</label>
                <select name="priority" required class="{{ $input }}">
                    @foreach (\App\Enums\Priority::cases() as $priority)
                        <option value="{{ $priority->value }}" @selected(old('priority', $asset->priority?->value ?? 'normal') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Received date') }} *</label>
                <input type="date" name="received_date" value="{{ old('received_date', $asset->received_date?->toDateString() ?? today()->toDateString()) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Expected release date') }}</label>
                <input type="date" name="expected_release_date" value="{{ old('expected_release_date', $asset->expected_release_date?->toDateString()) }}" class="{{ $input }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Reported problem') }} / {{ __('description') }}</label>
            <textarea name="description" rows="3" class="{{ $input }}">{{ old('description', $asset->description) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Notes') }}</label>
            <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $asset->notes) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $asset->exists ? __('Save changes') : __('Register asset') }}</button>
            <a href="{{ $asset->exists ? route('assets.show', $asset) : route('assets.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
