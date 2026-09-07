@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <h1 class="mb-6 text-xl font-semibold">{{ __('Dashboard') }}</h1>

    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ([
            ['Total Assets', $stats['assets_total'], 'text-slate-900'],
            ['In Factory', $stats['assets_in_factory'], 'text-blue-600'],
            ['Awaiting Inspection', $stats['awaiting_inspection'], 'text-amber-600'],
            ['Under Repair', $stats['under_repair'], 'text-orange-600'],
            ['Awaiting Testing', $stats['awaiting_testing'], 'text-purple-600'],
            ['Completed', $stats['completed'], 'text-green-600'],
            ['Released', $stats['released'], 'text-gray-500'],
            ['Open Work Orders', $stats['work_orders_open'], 'text-blue-600'],
            ['Overdue Work Orders', $stats['work_orders_overdue'], 'text-red-600'],
        ] as [$label, $value, $color])
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <p class="text-xs text-gray-500">{{ __($label) }}</p>
                <p class="mt-1 text-2xl font-semibold {{ $color }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h2 class="mb-3 font-semibold">{{ __('Recently Received Assets') }}</h2>
            <ul class="divide-y text-sm">
                @forelse ($recentAssets as $asset)
                    <li class="flex items-center justify-between py-2">
                        <div>
                            <a href="{{ route('assets.show', $asset) }}" class="font-medium text-blue-600 hover:underline">{{ $asset->asset_number }}</a>
                            <span class="text-gray-600">{{ $asset->name }}</span>
                            <p class="text-xs text-gray-500">{{ $asset->company->name }} &middot; {{ $asset->assetType->name }}</p>
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            <span class="rounded-full bg-gray-100 px-2 py-1">{{ $asset->status->label() }}</span>
                            <p class="mt-1">{{ $asset->received_date->format('Y-m-d') }}</p>
                        </div>
                    </li>
                @empty
                    <li class="py-2 text-gray-500">{{ __('No assets yet.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h2 class="mb-3 font-semibold">{{ __('Recent Activity') }}</h2>
            <ul class="space-y-3 text-sm">
                @forelse ($recentActivity as $activity)
                    <li class="border-l-2 border-blue-500 pl-3">
                        <p>{{ $activity->description }}</p>
                        <p class="text-xs text-gray-500">{{ $activity->user?->name ?? __('System') }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-gray-500">{{ __('No activity yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
