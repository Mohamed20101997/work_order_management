@extends('layouts.app')

@section('title', __('Work Orders'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ __('Work Orders') }}</h1>
        @can('work-orders.create')
            <a href="{{ route('work-orders.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('New Work Order') }}</a>
        @endcan
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="status" class="rounded-lg border border-gray-300 px-2 py-2 text-sm">
            <option value="">{{ __('All statuses') }}</option>
            @foreach (\App\Enums\WorkOrderStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="assigned_to" class="rounded-lg border border-gray-300 px-2 py-2 text-sm">
            <option value="">{{ __('All technicians') }}</option>
            @foreach ($technicians as $technician)
                <option value="{{ $technician->id }}" @selected(request('assigned_to') == $technician->id)>{{ $technician->name }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
            <input type="checkbox" name="overdue" value="1" @checked(request('overdue'))> {{ __('Overdue only') }}
        </label>
        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">{{ __('Filter') }}</button>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Number') }}</th>
                    <th class="px-4 py-3">{{ __('Asset') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Problem') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Assignee') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Due') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($workOrders as $workOrder)
                    <tr class="hover:bg-gray-50 {{ $workOrder->isOverdue() ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3"><a href="{{ route('work-orders.show', $workOrder) }}" class="font-medium text-blue-600 hover:underline">{{ $workOrder->number }}</a></td>
                        <td class="px-4 py-3">{{ $workOrder->asset->asset_number }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ Str::limit($workOrder->reported_problem, 60) }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $workOrder->status->label() }}</span></td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $workOrder->assignee?->name ?? __('Unassigned') }}</td>
                        <td class="hidden px-4 py-3 md:table-cell {{ $workOrder->isOverdue() ? 'font-medium text-red-600' : '' }}">
                            {{ $workOrder->due_date?->format('Y-m-d') ?? '-' }}{{ $workOrder->isOverdue() ? ' (' . __('overdue') . ')' : '' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">{{ __('No work orders found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $workOrders->links() }}</div>
@endsection
