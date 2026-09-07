@extends('layouts.app')

@section('title', $inspection->number)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ $inspection->number }}</h1>
        @can('inspections.manage')
            <a href="{{ route('inspections.edit', $inspection) }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">{{ __('Edit') }}</a>
        @endcan
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-4 shadow-sm lg:col-span-2">
            <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                <div><dt class="text-gray-500">{{ __('Asset') }}</dt><dd><a href="{{ route('assets.show', $inspection->asset) }}" class="text-blue-600 hover:underline">{{ $inspection->asset->asset_number }}</a></dd></div>
                <div><dt class="text-gray-500">{{ __('Work order') }}</dt><dd>
                    @if ($inspection->workOrder)
                        <a href="{{ route('work-orders.show', $inspection->workOrder) }}" class="text-blue-600 hover:underline">{{ $inspection->workOrder->number }}</a>
                    @else - @endif
                </dd></div>
                <div><dt class="text-gray-500">{{ __('Inspector') }}</dt><dd>{{ $inspection->inspector?->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Date') }}</dt><dd>{{ $inspection->inspection_date->format('Y-m-d') }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Result') }}</dt><dd><span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $inspection->result->label() }}</span></dd></div>
                <div><dt class="text-gray-500">{{ __('Status') }}</dt><dd>{{ ucfirst($inspection->status) }}</dd></div>
            </dl>
            @if ($inspection->notes)
                <p class="mt-4 text-sm"><span class="text-gray-500">{{ __('Notes:') }}</span> {{ $inspection->notes }}</p>
            @endif
        </div>
        <div>
            @include('partials.attachments', ['attachable' => $inspection, 'type' => 'inspections'])
        </div>
    </div>
@endsection
