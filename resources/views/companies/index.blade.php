@extends('layouts.app')

@section('title', __('Companies'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">{{ __('Companies') }}</h1>
        @can('companies.manage')
            <a href="{{ route('companies.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">{{ __('Add Company') }}</a>
        @endcan
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search companies...') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">{{ __('Search') }}</button>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Contact') }}</th>
                    <th class="hidden px-4 py-3 md:table-cell">{{ __('Phone') }}</th>
                    <th class="px-4 py-3">{{ __('Assets') }}</th>
                    <th class="px-4 py-3">{{ __('Active') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($companies as $company)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><a href="{{ route('companies.show', $company) }}" class="font-medium text-blue-600 hover:underline">{{ $company->name }}</a></td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $company->contact_person ?? '-' }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $company->phone ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $company->assets_count }}</td>
                        <td class="px-4 py-3">{{ $company->is_active ? __('Yes') : __('No') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">{{ __('No companies found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $companies->links() }}</div>
@endsection
