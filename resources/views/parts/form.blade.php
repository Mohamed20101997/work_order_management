@extends('layouts.app')

@section('title', $part->exists ? __('Edit Part') : __('Add Part'))

@section('content')
    @php($input = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500')
    <h1 class="mb-4 text-xl font-semibold">{{ $part->exists ? __("Edit {$part->part_number}") : __('Add Part') }}</h1>

    <form method="POST" action="{{ $part->exists ? route('parts.update', $part) : route('parts.store') }}" class="max-w-2xl space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($part->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Part number') }} *</label>
                <input type="text" name="part_number" value="{{ old('part_number', $part->part_number) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Name') }} *</label>
                <input type="text" name="name" value="{{ old('name', $part->name) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Quantity') }} *</label>
                <input type="number" name="quantity" min="0" value="{{ old('quantity', $part->quantity ?? 0) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Minimum quantity') }} *</label>
                <input type="number" name="minimum_quantity" min="0" value="{{ old('minimum_quantity', $part->minimum_quantity ?? 0) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Unit') }} *</label>
                <input type="text" name="unit" value="{{ old('unit', $part->unit ?? 'pcs') }}" required class="{{ $input }}">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Description') }}</label>
            <textarea name="description" rows="2" class="{{ $input }}">{{ old('description', $part->description) }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $part->is_active ?? true))> {{ __('Active') }}
        </label>
        <div class="flex gap-3">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $part->exists ? __('Save changes') : __('Create part') }}</button>
            <a href="{{ route('parts.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
