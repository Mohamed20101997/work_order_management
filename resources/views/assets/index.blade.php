@extends('layouts.app')

@section('title', __('Assets'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ __('Assets') }}</h1>
        @can('assets.create')
            <a href="{{ route('assets.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('Register Asset') }}</a>
        @endcan
    </div>

    <form method="GET" class="mb-4 grid grid-cols-2 gap-2 md:grid-cols-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search number, name, serial...') }}"
               class="col-span-2 rounded-lg border border-gray-300 px-3 py-2 text-sm md:col-span-2">
        <select name="status" class="rounded-lg border border-gray-300 px-2 py-2 text-sm">
            <option value="">{{ __('All statuses') }}</option>
            @foreach (\App\Enums\AssetStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="company_id" class="rounded-lg border border-gray-300 px-2 py-2 text-sm">
            <option value="">{{ __('All companies') }}</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(request('company_id') == $company->id)>{{ $company->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <select name="asset_type_id" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm">
                <option value="">{{ __('All types') }}</option>
                @foreach ($assetTypes as $type)
                    <option value="{{ $type->id }}" @selected(request('asset_type_id') == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">{{ __('Filter') }}</button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Number') }}</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Company') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Type') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Received') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Priority') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($assets as $asset)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><a href="{{ route('assets.show', $asset) }}" class="font-medium text-blue-600 hover:underline">{{ $asset->asset_number }}</a></td>
                        <td class="px-4 py-3">{{ $asset->name }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $asset->company->name }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $asset->assetType->name }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ $asset->status->label() }}</span></td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $asset->received_date->format('Y-m-d') }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $asset->priority->label() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">{{ __('No assets found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $assets->links() }}</div>
@endsection
