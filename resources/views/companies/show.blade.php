@extends('layouts.app')

@section('title', $company->name)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ $company->name }}</h1>
        @can('companies.manage')
            <a href="{{ route('companies.edit', $company) }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">{{ __('Edit') }}</a>
        @endcan
    </div>

    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm">
        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm md:grid-cols-4">
            <div><dt class="text-gray-500">{{ __('Contact person') }}</dt><dd>{{ $company->contact_person ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">{{ __('Phone') }}</dt><dd>{{ $company->phone ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">{{ __('Email') }}</dt><dd>{{ $company->email ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">{{ __('Active') }}</dt><dd>{{ $company->is_active ? __('Yes') : __('No') }}</dd></div>
        </dl>
        @if ($company->address)
            <p class="mt-3 text-sm"><span class="text-gray-500">{{ __('Address:') }}</span> {{ $company->address }}</p>
        @endif
        @if ($company->notes)
            <p class="mt-2 text-sm"><span class="text-gray-500">{{ __('Notes:') }}</span> {{ $company->notes }}</p>
        @endif
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <h2 class="mb-3 font-semibold">{{ __('Recent Assets') }}</h2>
        <ul class="divide-y text-sm">
            @forelse ($company->assets as $asset)
                <li class="flex items-center justify-between py-2">
                    <div>
                        <a href="{{ route('assets.show', $asset) }}" class="font-medium text-blue-600 hover:underline">{{ $asset->asset_number }}</a>
                        <span class="text-gray-600">{{ $asset->name }}</span>
                    </div>
                    <div class="text-right text-xs text-gray-500">
                        <span class="rounded-full bg-gray-100 px-2 py-1">{{ $asset->status->label() }}</span>
                        <p class="mt-1">{{ $asset->received_date->format('Y-m-d') }}</p>
                    </div>
                </li>
            @empty
                <li class="py-2 text-gray-500">{{ __('No assets for this company.') }}</li>
            @endforelse
        </ul>
    </div>
@endsection
