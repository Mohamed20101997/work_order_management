@extends('layouts.app')

@section('title', $workOrder->exists ? __('Edit Work Order') : __('New Work Order'))

@section('content')
    @php($input = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500')
    @php($fullEdit = auth()->user()->can('work-orders.update') || ! $workOrder->exists)
    <h1 class="mb-4 text-xl font-semibold">{{ $workOrder->exists ? __("Edit {$workOrder->number}") : __('New Work Order') }}</h1>

    <form method="POST" action="{{ $workOrder->exists ? route('work-orders.update', $workOrder) : route('work-orders.store') }}" class="max-w-3xl space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($workOrder->exists) @method('PUT') @endif

        @if (! $workOrder->exists)
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Asset') }} *</label>
                <select name="asset_id" required class="{{ $input }}">
                    <option value="">{{ __('Select asset') }}</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id', $workOrder->asset_id) == $asset->id)>{{ $asset->asset_number }} - {{ $asset->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($fullEdit)
            <div>
                <label class="mb-1 block text-sm font-medium">{{ __('Reported problem') }} *</label>
                <textarea name="reported_problem" rows="3" required class="{{ $input }}">{{ old('reported_problem', $workOrder->reported_problem) }}</textarea>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Priority') }} *</label>
                    <select name="priority" required class="{{ $input }}">
                        @foreach (\App\Enums\Priority::cases() as $priority)
                            <option value="{{ $priority->value }}" @selected(old('priority', $workOrder->priority?->value ?? 'normal') === $priority->value)>{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Assign to') }}</label>
                    <select name="assigned_to" class="{{ $input }}">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician->id }}" @selected(old('assigned_to', $workOrder->assigned_to) == $technician->id)>{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">{{ __('Due date') }}</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $workOrder->due_date?->toDateString()) }}" class="{{ $input }}">
                </div>
            </div>
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Diagnosis') }}</label>
            <textarea name="diagnosis" rows="3" class="{{ $input }}">{{ old('diagnosis', $workOrder->diagnosis) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Work performed') }}</label>
            <textarea name="work_performed" rows="3" class="{{ $input }}">{{ old('work_performed', $workOrder->work_performed) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">{{ __('Repair notes') }}</label>
            <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $workOrder->notes) }}</textarea>
        </div>
        <div class="flex gap-3">
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ $workOrder->exists ? __('Save changes') : __('Create work order') }}</button>
            <a href="{{ $workOrder->exists ? route('work-orders.show', $workOrder) : route('work-orders.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
