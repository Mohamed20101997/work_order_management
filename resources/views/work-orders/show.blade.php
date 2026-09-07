@extends('layouts.app')

@section('title', $workOrder->number)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">{{ $workOrder->number }}</h1>
            <p class="text-sm text-gray-500">
                {{ __('Asset') }}: <a href="{{ route('assets.show', $workOrder->asset) }}" class="text-blue-600 hover:underline">{{ $workOrder->asset->asset_number }}</a>
                &middot; {{ $workOrder->asset->company->name }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if (auth()->user()->can('work-orders.update') || (auth()->user()->can('work-orders.notes') && $workOrder->assigned_to === auth()->id()))
                <a href="{{ route('work-orders.edit', $workOrder) }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">{{ __('Edit') }}</a>
            @endif
            @can('inspections.manage')
                <a href="{{ route('inspections.create', ['asset_id' => $workOrder->asset_id, 'work_order_id' => $workOrder->id]) }}" class="rounded-lg bg-slate-800 px-3 py-2 text-sm text-white">{{ __('New Inspection') }}</a>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">{{ $workOrder->status->label() }}</span>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm">{{ __('Priority') }}: {{ $workOrder->priority->label() }}</span>
                    @if ($workOrder->isOverdue())
                        <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-700">{{ __('Overdue') }}</span>
                    @endif
                </div>

                @can('work-orders.status')
                    @if ($workOrder->status->transitions() && (! auth()->user()->isTechnician() || $workOrder->assigned_to === auth()->id()))
                        <form method="POST" action="{{ route('work-orders.status', $workOrder) }}" class="mb-4 flex flex-wrap gap-2">
                            @csrf
                            <span class="self-center text-sm text-gray-500">{{ __('Move to:') }}</span>
                            @foreach ($workOrder->status->transitions() as $transition)
                                <button name="status" value="{{ $transition->value }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700">{{ $transition->label() }}</button>
                            @endforeach
                        </form>
                    @endif
                @endcan

                @can('work-orders.assign')
                    <form method="POST" action="{{ route('work-orders.assign', $workOrder) }}" class="mb-4 flex flex-wrap items-center gap-2">
                        @csrf
                        <label class="text-sm text-gray-500">{{ __('Assign to:') }}</label>
                        <select name="assigned_to" required class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                            @foreach ($technicians as $technician)
                                <option value="{{ $technician->id }}" @selected($workOrder->assigned_to === $technician->id)>{{ $technician->name }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white">{{ __('Assign') }}</button>
                    </form>
                @endcan

                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm md:grid-cols-3">
                    <div><dt class="text-gray-500">{{ __('Assignee') }}</dt><dd>{{ $workOrder->assignee?->name ?? __('Unassigned') }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Due date') }}</dt><dd>{{ $workOrder->due_date?->format('Y-m-d') ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Created by') }}</dt><dd>{{ $workOrder->creator?->name ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Started') }}</dt><dd>{{ $workOrder->started_at?->format('Y-m-d H:i') ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Completed') }}</dt><dd>{{ $workOrder->completed_at?->format('Y-m-d H:i') ?? '-' }}</dd></div>
                </dl>

                <div class="mt-4 space-y-3 text-sm">
                    <p><span class="font-medium">{{ __('Reported problem:') }}</span> {{ $workOrder->reported_problem }}</p>
                    <p><span class="font-medium">{{ __('Diagnosis:') }}</span> {{ $workOrder->diagnosis ?? '-' }}</p>
                    <p><span class="font-medium">{{ __('Work performed:') }}</span> {{ $workOrder->work_performed ?? '-' }}</p>
                    <p><span class="font-medium">{{ __('Notes:') }}</span> {{ $workOrder->notes ?? '-' }}</p>
                </div>
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm">
                <h3 class="mb-3 font-semibold">{{ __('Parts Used') }}</h3>
                <ul class="divide-y text-sm">
                    @forelse ($workOrder->partUsages as $usage)
                        <li class="flex items-center justify-between py-2">
                            <span>{{ $usage->part->name }} ({{ $usage->part->part_number }})</span>
                            <span class="text-xs text-gray-500">{{ $usage->quantity }} {{ $usage->part->unit }} &middot; {{ $usage->user?->name }} &middot; {{ $usage->used_at->format('Y-m-d') }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500">{{ __('No parts recorded.') }}</li>
                    @endforelse
                </ul>
                @can('parts.use')
                    <form method="POST" action="{{ route('work-orders.parts.store', $workOrder) }}" class="mt-4 flex flex-wrap items-center gap-2">
                        @csrf
                        <select name="part_id" required class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                            <option value="">{{ __('Select part') }}</option>
                            @foreach ($parts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }} ({{ __('stock') }}: {{ $part->quantity }} {{ $part->unit }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="quantity" min="1" value="1" required class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                        <button class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white">{{ __('Record usage') }}</button>
                    </form>
                @endcan
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm">
                <h3 class="mb-3 font-semibold">{{ __('Inspections') }}</h3>
                <ul class="divide-y text-sm">
                    @forelse ($workOrder->inspections as $inspection)
                        <li class="flex items-center justify-between py-2">
                            <a href="{{ route('inspections.show', $inspection) }}" class="font-medium text-blue-600 hover:underline">{{ $inspection->number }}</a>
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $inspection->result->label() }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500">{{ __('No inspections.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            @include('partials.attachments', ['attachable' => $workOrder, 'type' => 'work-orders'])
            @include('partials.activities', ['activities' => $activities])
        </div>
    </div>
@endsection
