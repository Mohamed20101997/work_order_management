@extends('layouts.app')

@section('title', __('Reports'))

@section('content')
    <h1 class="mb-6 text-xl font-semibold">{{ __('Reports') }}</h1>

    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500">{{ __('Received (last 30 days)') }}</p>
            <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $receivedLast30 }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500">{{ __('Released (last 30 days)') }}</p>
            <p class="mt-1 text-2xl font-semibold text-green-600">{{ $releasedLast30 }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h2 class="mb-3 font-semibold">{{ __('Assets by Status') }}</h2>
            <ul class="divide-y text-sm">
                @foreach (\App\Enums\AssetStatus::cases() as $status)
                    <li class="flex justify-between py-2">
                        <span>{{ $status->label() }}</span>
                        <span class="font-medium">{{ $assetsByStatus[$status->value] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h2 class="mb-3 font-semibold">{{ __('Work Orders by Status') }}</h2>
            <ul class="divide-y text-sm">
                @foreach (\App\Enums\WorkOrderStatus::cases() as $status)
                    <li class="flex justify-between py-2">
                        <span>{{ $status->label() }}</span>
                        <span class="font-medium">{{ $workOrdersByStatus[$status->value] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h2 class="mb-3 font-semibold">{{ __('Technician Workload') }} ({{ __('open orders') }})</h2>
            <ul class="divide-y text-sm">
                @forelse ($technicianLoad as $technician)
                    <li class="flex justify-between py-2">
                        <span>{{ $technician->name }}</span>
                        <span class="font-medium">{{ $technician->open_work_orders }}</span>
                    </li>
                @empty
                    <li class="py-2 text-gray-500">{{ __('No technicians.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h2 class="mb-3 font-semibold">{{ __('Low Stock Parts') }}</h2>
            <ul class="divide-y text-sm">
                @forelse ($lowStock as $part)
                    <li class="flex justify-between py-2">
                        <span>{{ $part->name }} ({{ $part->part_number }})</span>
                        <span class="font-medium text-amber-700">{{ $part->quantity }} / {{ __('Min') }} {{ $part->minimum_quantity }}</span>
                    </li>
                @empty
                    <li class="py-2 text-gray-500">{{ __('All parts above minimum stock.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mt-6 rounded-xl bg-white p-4 shadow-sm">
        <h2 class="mb-3 font-semibold">{{ __('Overdue Work Orders') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2">{{ __('Number') }}</th>
                        <th class="px-3 py-2">{{ __('Asset') }}</th>
                        <th class="px-3 py-2">{{ __('Assignee') }}</th>
                        <th class="px-3 py-2">{{ __('Due date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($overdue as $workOrder)
                        <tr>
                            <td class="px-3 py-2"><a href="{{ route('work-orders.show', $workOrder) }}" class="text-blue-600 hover:underline">{{ $workOrder->number }}</a></td>
                            <td class="px-3 py-2">{{ $workOrder->asset->asset_number }}</td>
                            <td class="px-3 py-2">{{ $workOrder->assignee?->name ?? __('Unassigned') }}</td>
                            <td class="px-3 py-2 font-medium text-red-600">{{ $workOrder->due_date->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">{{ __('No overdue work orders.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
