<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Part;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        Gate::authorize('reports.view');

        return view('reports.index', [
            'assetsByStatus' => Asset::query()->toBase()
                ->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'workOrdersByStatus' => WorkOrder::query()->toBase()
                ->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'technicianLoad' => User::where('role', 'technician')
                ->withCount(['assignedWorkOrders as open_work_orders' => fn ($query) => $query->open()])
                ->orderBy('name')->get(),
            'overdue' => WorkOrder::query()->overdue()->with(['asset', 'assignee'])->orderBy('due_date')->limit(25)->get(),
            'receivedLast30' => Asset::whereDate('received_date', '>=', today()->subDays(30))->count(),
            'releasedLast30' => Asset::whereNotNull('released_date')->whereDate('released_date', '>=', today()->subDays(30))->count(),
            'lowStock' => Part::whereColumn('quantity', '<=', 'minimum_quantity')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
