<a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-white">{{ __('Dashboard') }}</a>
@can('assets.view')
    <a href="{{ route('assets.index') }}" class="text-slate-300 hover:text-white">{{ __('Assets') }}</a>
@endcan
@can('work-orders.view')
    <a href="{{ route('work-orders.index') }}" class="text-slate-300 hover:text-white">{{ __('Work Orders') }}</a>
@endcan
@can('inspections.view')
    <a href="{{ route('inspections.index') }}" class="text-slate-300 hover:text-white">{{ __('Inspections') }}</a>
@endcan
@can('parts.view')
    <a href="{{ route('parts.index') }}" class="text-slate-300 hover:text-white">{{ __('Parts') }}</a>
@endcan
@can('companies.view')
    <a href="{{ route('companies.index') }}" class="text-slate-300 hover:text-white">{{ __('Companies') }}</a>
@endcan
@can('reports.view')
    <a href="{{ route('reports.index') }}" class="text-slate-300 hover:text-white">{{ __('Reports') }}</a>
@endcan
@can('users.manage')
    <a href="{{ route('users.index') }}" class="text-slate-300 hover:text-white">{{ __('Users') }}</a>
@endcan
