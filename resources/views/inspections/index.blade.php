@extends('layouts.app')

@section('title', __('Inspections'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ __('Inspections') }}</h1>
        @can('inspections.manage')
            <a href="{{ route('inspections.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('New Inspection') }}</a>
        @endcan
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <select name="result" class="rounded-lg border border-gray-300 px-2 py-2 text-sm">
            <option value="">{{ __('All results') }}</option>
            @foreach (\App\Enums\InspectionResult::cases() as $result)
                <option value="{{ $result->value }}" @selected(request('result') === $result->value)>{{ $result->label() }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">{{ __('Filter') }}</button>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Number') }}</th>
                    <th class="px-4 py-3">{{ __('Asset') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Work Order') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Inspector') }}</th>
                    <th class="px-4 py-3">{{ __('Date') }}</th>
                    <th class="px-4 py-3">{{ __('Result') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($inspections as $inspection)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><a href="{{ route('inspections.show', $inspection) }}" class="font-medium text-blue-600 hover:underline">{{ $inspection->number }}</a></td>
                        <td class="px-4 py-3">{{ $inspection->asset->asset_number }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $inspection->workOrder?->number ?? '-' }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $inspection->inspector?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $inspection->inspection_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $inspection->result->label() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">{{ __('No inspections found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $inspections->links() }}</div>
@endsection
