@extends('layouts.app')

@section('title', __('Parts'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ __('Parts Inventory') }}</h1>
        @can('parts.manage')
            <a href="{{ route('parts.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('Add Part') }}</a>
        @endcan
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search parts...') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">{{ __('Search') }}</button>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Part #') }}</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Stock') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Min') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Active') }}</th>
                    @can('parts.manage')<th class="px-4 py-3"></th>@endcan
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($parts as $part)
                    <tr class="hover:bg-gray-50 {{ $part->isLowStock() ? 'bg-amber-50' : '' }}">
                        <td class="px-4 py-3 font-medium">{{ $part->part_number }}</td>
                        <td class="px-4 py-3">{{ $part->name }}</td>
                        <td class="px-4 py-3 {{ $part->isLowStock() ? 'font-medium text-amber-700' : '' }}">
                            {{ $part->quantity }} {{ $part->unit }}{{ $part->isLowStock() ? ' (' . __('low') . ')' : '' }}
                        </td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $part->minimum_quantity }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $part->is_active ? __('Yes') : __('No') }}</td>
                        @can('parts.manage')
                            <td class="px-4 py-3"><a href="{{ route('parts.edit', $part) }}" class="text-blue-600 hover:underline">{{ __('Edit') }}</a></td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">{{ __('No parts found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $parts->links() }}</div>
@endsection
