<?php

namespace App\Http\Controllers;

use App\Http\Requests\InspectionRequest;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\Inspection;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InspectionController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('inspections.view');

        $inspections = Inspection::with(['asset', 'inspector', 'workOrder'])
            ->when($request->string('result')->toString(), fn ($query, $result) => $query->where('result', $result))
            ->latest('inspection_date')->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('inspections.index', compact('inspections'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('inspections.manage');

        return $this->form(new Inspection([
            'asset_id' => $request->integer('asset_id') ?: null,
            'work_order_id' => $request->integer('work_order_id') ?: null,
            'inspection_date' => today()->toDateString(),
        ]));
    }

    public function store(InspectionRequest $request): RedirectResponse
    {
        Gate::authorize('inspections.manage');

        $inspection = DB::transaction(function () use ($request): Inspection {
            $inspection = Inspection::create([
                ...$request->validated(),
                'inspector_id' => $request->user()->id,
                'status' => 'completed',
            ]);
            $inspection->assignNumber();

            Activity::record($inspection->asset, 'inspected', "Inspection {$inspection->number} recorded: {$inspection->result->label()}");

            return $inspection;
        });

        return redirect()->route('inspections.show', $inspection)->with('success', "Inspection {$inspection->number} recorded.");
    }

    public function show(Inspection $inspection): View
    {
        Gate::authorize('inspections.view');

        $inspection->load(['asset', 'workOrder', 'inspector', 'attachments.user']);

        return view('inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection): View
    {
        Gate::authorize('inspections.manage');

        return $this->form($inspection);
    }

    public function update(InspectionRequest $request, Inspection $inspection): RedirectResponse
    {
        Gate::authorize('inspections.manage');

        $inspection->update($request->validated());

        return redirect()->route('inspections.show', $inspection)->with('success', 'Inspection updated.');
    }

    private function form(Inspection $inspection): View
    {
        return view('inspections.form', [
            'inspection' => $inspection,
            'assets' => Asset::orderByDesc('id')->get(['id', 'asset_number', 'name']),
            'workOrders' => WorkOrder::orderByDesc('id')->limit(100)->get(['id', 'number']),
        ]);
    }
}
