@extends('layouts.app')

@section('title', $inspection->exists ? __('Edit Inspection') : __('New Inspection'))

@section('content')
    @php($input = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500')
    <h1 class="mb-4 text-xl font-semibold">{{ $inspection->exists ? __("Edit {$inspection->number}") : __('New Inspection') }}</h1>

    <form method="POST" action="{{ $inspection->exists ? route('inspections.update', $inspection) : route('inspections.store') }}" class="max-w-2xl space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($inspection->exists) @method('PUT') @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Asset') }} *</label>
                <select name="asset_id" required class="{{ $input }}">
                    <option value="">{{ __('Select asset') }}</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id', $inspection->asset_id) == $asset->id)>{{ $asset->asset_number }} - {{ $asset->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Work order') }} ({{ __('optional') }})</label>
                <select name="work_order_id" class="{{ $input }}">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($workOrders as $workOrder)
                        <option value="{{ $workOrder->id }}" @selected(old('work_order_id', $inspection->work_order_id) == $workOrder->id)>{{ $workOrder->number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Inspection date') }} *</label>
                <input type="date" name="inspection_date" value="{{ old('inspection_date', $inspection->inspection_date instanceof \Carbon\CarbonInterface ? $inspection->inspection_date->toDateString() : $inspection->inspection_date) }}" required class="{{ $input }}">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Result') }} *</label>
                <select name="result" required class="{{ $input }}">
                    <option value="">{{ __('Select result') }}</option>
                    @foreach (\App\Enums\InspectionResult::cases() as $result)
                        <option value="{{ $result->value }}" @selected(old('result', $inspection->result?->value) === $result->value)>{{ $result->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Notes') }}</label>
            <textarea name="notes" rows="3" class="{{ $input }}">{{ old('notes', $inspection->notes) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $inspection->exists ? __('Save changes') : __('Record inspection') }}</button>
            <a href="{{ route('inspections.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
