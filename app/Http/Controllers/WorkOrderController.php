<?php

namespace App\Http\Controllers;

use App\Enums\WorkOrderStatus;
use App\Http\Requests\WorkOrderRequest;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\Part;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('work-orders.view');

        $workOrders = WorkOrder::with(['asset.company', 'assignee'])
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->integer('assigned_to'), fn ($query, $userId) => $query->where('assigned_to', $userId))
            ->when($request->boolean('overdue'), fn ($query) => $query->overdue())
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('work-orders.index', [
            'workOrders' => $workOrders,
            'technicians' => $this->technicians(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('work-orders.create');

        return view('work-orders.form', [
            'workOrder' => new WorkOrder(['asset_id' => $request->integer('asset_id') ?: null]),
            'assets' => Asset::orderByDesc('id')->get(['id', 'asset_number', 'name']),
            'technicians' => $this->technicians(),
        ]);
    }

    public function store(WorkOrderRequest $request): RedirectResponse
    {
        Gate::authorize('work-orders.create');

        $workOrder = DB::transaction(function () use ($request): WorkOrder {
            $workOrder = WorkOrder::create([...$request->validated(), 'status' => WorkOrderStatus::Open]);
            $workOrder->assignNumber();

            Activity::record($workOrder, 'created', "Work order {$workOrder->number} created");
            Activity::record($workOrder->asset, 'work_order_created', "Work order {$workOrder->number} opened for this asset");

            return $workOrder;
        });

        Cache::forget('dashboard.stats');

        return redirect()->route('work-orders.show', $workOrder)->with('success', "Work order {$workOrder->number} created.");
    }

    public function show(WorkOrder $workOrder): View
    {
        Gate::authorize('work-orders.view');

        $workOrder->load(['asset.company', 'assignee', 'creator', 'partUsages.part', 'partUsages.user', 'inspections.inspector', 'attachments.user']);

        return view('work-orders.show', [
            'workOrder' => $workOrder,
            'technicians' => $this->technicians(),
            'parts' => Part::where('is_active', true)->orderBy('name')->get(['id', 'name', 'part_number', 'quantity', 'unit']),
            'activities' => $workOrder->activities()->with('user')->limit(30)->get(),
        ]);
    }

    public function edit(WorkOrder $workOrder): View
    {
        $this->authorizeEdit($workOrder);

        return view('work-orders.form', [
            'workOrder' => $workOrder,
            'assets' => Asset::orderByDesc('id')->get(['id', 'asset_number', 'name']),
            'technicians' => $this->technicians(),
        ]);
    }

    public function update(WorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $data = $request->validated();

        if (Gate::allows('work-orders.update')) {
            $workOrder->update(Arr::only($data, [
                'reported_problem', 'diagnosis', 'work_performed', 'notes', 'priority', 'assigned_to', 'due_date',
            ]));
        } elseif (Gate::allows('work-orders.notes') && $workOrder->assigned_to === $request->user()->id) {
            $workOrder->update(Arr::only($data, ['diagnosis', 'work_performed', 'notes']));
        } else {
            abort(403);
        }

        Activity::record($workOrder, 'updated', "Work order {$workOrder->number} updated");

        return redirect()->route('work-orders.show', $workOrder)->with('success', 'Work order updated.');
    }

    public function assign(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('work-orders.assign');

        $data = $request->validate(['assigned_to' => ['required', 'exists:users,id']]);
        $assignee = User::findOrFail($data['assigned_to']);

        $workOrder->update(['assigned_to' => $assignee->id]);
        Activity::record($workOrder, 'assigned', "Work order {$workOrder->number} assigned to {$assignee->name}");

        return back()->with('success', "Assigned to {$assignee->name}.");
    }

    public function updateStatus(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('work-orders.status');

        // Technicians may only progress work orders assigned to them.
        if ($request->user()->isTechnician() && $workOrder->assigned_to !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate(['status' => ['required', Rule::enum(WorkOrderStatus::class)]]);
        $to = WorkOrderStatus::from($data['status']);

        if (! $workOrder->status->canTransitionTo($to)) {
            throw ValidationException::withMessages([
                'status' => "Cannot change status from {$workOrder->status->label()} to {$to->label()}.",
            ]);
        }

        DB::transaction(function () use ($workOrder, $to): void {
            $workOrder->update([
                'status' => $to,
                'started_at' => $workOrder->started_at ?? ($to === WorkOrderStatus::InProgress ? now() : null),
                'completed_at' => $to === WorkOrderStatus::Completed ? now() : null,
            ]);

            Activity::record($workOrder, 'status_changed', "Work order {$workOrder->number} moved to {$to->label()}", ['to' => $to->value]);
            Activity::record($workOrder->asset, 'work_order_updated', "Work order {$workOrder->number} moved to {$to->label()}");
        });

        Cache::forget('dashboard.stats');

        return back()->with('success', "Work order moved to {$to->label()}.");
    }

    private function authorizeEdit(WorkOrder $workOrder): void
    {
        $user = auth()->user();

        abort_unless(
            Gate::allows('work-orders.update')
            || (Gate::allows('work-orders.notes') && $workOrder->assigned_to === $user->id),
            403
        );
    }

    private function technicians()
    {
        return User::where('is_active', true)
            ->whereIn('role', ['technician', 'manager'])
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
