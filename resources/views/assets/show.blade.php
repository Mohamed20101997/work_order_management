@extends('layouts.app')

@section('title', $asset->asset_number)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">{{ $asset->asset_number }} &middot; {{ $asset->name }}</h1>
            <p class="text-sm text-gray-500">{{ $asset->assetType->name }} &middot; {{ $asset->company->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('work-orders.create')
                <a href="{{ route('work-orders.create', ['asset_id' => $asset->id]) }}" class="rounded-lg bg-slate-800 px-3 py-2 text-sm text-white">{{ __('New Work Order') }}</a>
            @endcan
            @can('inspections.manage')
                <a href="{{ route('inspections.create', ['asset_id' => $asset->id]) }}" class="rounded-lg bg-slate-800 px-3 py-2 text-sm text-white">{{ __('New Inspection') }}</a>
            @endcan
            @can('assets.update')
                <a href="{{ route('assets.edit', $asset) }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">{{ __('Edit') }}</a>
            @endcan
            @can('assets.delete')
                <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm(__('Delete this asset and all related records?'))">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-lg border border-red-300 bg-white px-3 py-2 text-sm text-red-600">{{ __('Delete') }}</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">{{ $asset->status->label() }}</span>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm">{{ __('Priority') }}: {{ $asset->priority->label() }}</span>
                </div>
                @can('assets.status')
                    @if ($asset->status->transitions())
                        <form method="POST" action="{{ route('assets.status', $asset) }}" class="mb-4 flex flex-wrap gap-2">
                            @csrf
                            <span class="self-center text-sm text-gray-500">{{ __('Move to:') }}</span>
                            @foreach ($asset->status->transitions() as $transition)
                                <button name="status" value="{{ $transition->value }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700">{{ $transition->label() }}</button>
                            @endforeach
                        </form>
                    @endif
                @endcan
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm md:grid-cols-3">
                    <div><dt class="text-gray-500">{{ __('Serial number') }}</dt><dd>{{ $asset->serial_number ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Manufacturer') }}</dt><dd>{{ $asset->manufacturer ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Model') }}</dt><dd>{{ $asset->model ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Location') }}</dt><dd>{{ $asset->location ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Received') }}</dt><dd>{{ $asset->received_date->format('Y-m-d') }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Expected release') }}</dt><dd>{{ $asset->expected_release_date?->format('Y-m-d') ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Released') }}</dt><dd>{{ $asset->released_date?->format('Y-m-d') ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Registered by') }}</dt><dd>{{ $asset->creator?->name ?? '-' }}</dd></div>
                </dl>
                @if ($asset->description)
                    <p class="mt-4 text-sm"><span class="text-gray-500">{{ __('Reported problem:') }}</span> {{ $asset->description }}</p>
                @endif
                @if ($asset->notes)
                    <p class="mt-2 text-sm"><span class="text-gray-500">{{ __('Notes:') }}</span> {{ $asset->notes }}</p>
                @endif
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm">
                <h3 class="mb-3 font-semibold">{{ __('Work Orders') }}</h3>
                <ul class="divide-y text-sm">
                    @forelse ($asset->workOrders as $workOrder)
                        <li class="flex items-center justify-between py-2">
                            <div>
                                <a href="{{ route('work-orders.show', $workOrder) }}" class="font-medium text-blue-600 hover:underline">{{ $workOrder->number }}</a>
                                <p class="text-xs text-gray-500">{{ Str::limit($workOrder->reported_problem, 80) }}</p>
                            </div>
                            <div class="text-right text-xs">
                                <span class="rounded-full bg-gray-100 px-2 py-1">{{ $workOrder->status->label() }}</span>
                                <p class="mt-1 text-gray-500">{{ $workOrder->assignee?->name ?? __('Unassigned') }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500">{{ __('No work orders.') }}</li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm">
                <h3 class="mb-3 font-semibold">{{ __('Inspections') }}</h3>
                <ul class="divide-y text-sm">
                    @forelse ($asset->inspections as $inspection)
                        <li class="flex items-center justify-between py-2">
                            <a href="{{ route('inspections.show', $inspection) }}" class="font-medium text-blue-600 hover:underline">{{ $inspection->number }}</a>
                            <span class="text-xs text-gray-500">{{ $inspection->inspection_date->format('Y-m-d') }} &middot; {{ $inspection->inspector?->name }}</span>
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $inspection->result->label() }}</span>
                        </li>
                    @empty
                        <li class="py-2 text-gray-500">{{ __('No inspections.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="space-y-6">
            @include('partials.attachments', ['attachable' => $asset, 'type' => 'assets'])
            @include('partials.activities', ['activities' => $activities])
        </div>
    </div>
@endsection
