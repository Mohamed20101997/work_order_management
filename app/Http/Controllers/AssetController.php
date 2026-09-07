<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Http\Requests\AssetRequest;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('assets.view');

        $assets = Asset::with(['company', 'assetType'])
            ->search($request->string('q')->toString())
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->integer('company_id'), fn ($query, $companyId) => $query->where('company_id', $companyId))
            ->when($request->integer('asset_type_id'), fn ($query, $typeId) => $query->where('asset_type_id', $typeId))
            ->latest('received_date')->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('assets.index', [
            'assets' => $assets,
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('assets.create');

        return $this->form(new Asset());
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        Gate::authorize('assets.create');

        $asset = DB::transaction(function () use ($request): Asset {
            $asset = Asset::create([...$request->validated(), 'status' => AssetStatus::Received]);
            $asset->assignNumber();
            Activity::record($asset, 'received', "Asset {$asset->asset_number} received");

            return $asset;
        });

        Cache::forget('dashboard.stats');

        return redirect()->route('assets.show', $asset)->with('success', "Asset {$asset->asset_number} registered.");
    }

    public function show(Asset $asset): View
    {
        Gate::authorize('assets.view');

        $asset->load(['company', 'assetType', 'creator', 'workOrders.assignee', 'inspections.inspector', 'attachments.user']);

        return view('assets.show', [
            'asset' => $asset,
            'activities' => $asset->activities()->with('user')->limit(30)->get(),
        ]);
    }

    public function edit(Asset $asset): View
    {
        Gate::authorize('assets.update');

        return $this->form($asset);
    }

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        Gate::authorize('assets.update');

        $asset->update($request->validated());
        Activity::record($asset, 'updated', "Asset {$asset->asset_number} details updated");

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated.');
    }

    public function updateStatus(Request $request, Asset $asset): RedirectResponse
    {
        Gate::authorize('assets.status');

        $data = $request->validate(['status' => ['required', Rule::enum(AssetStatus::class)]]);
        $to = AssetStatus::from($data['status']);

        if (! $asset->status->canTransitionTo($to)) {
            throw ValidationException::withMessages([
                'status' => "Cannot change status from {$asset->status->label()} to {$to->label()}.",
            ]);
        }

        DB::transaction(function () use ($asset, $to): void {
            $asset->update([
                'status' => $to,
                'released_date' => $to === AssetStatus::Released ? today() : $asset->released_date,
            ]);
            Activity::record($asset, 'status_changed', "Status changed to {$to->label()}", ['to' => $to->value]);
        });

        Cache::forget('dashboard.stats');

        return back()->with('success', "Status changed to {$to->label()}.");
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        Gate::authorize('assets.delete');

        DB::transaction(function () use ($asset): void {
            $asset->attachments()->delete();
            $asset->activities()->delete();
            $asset->delete();
        });

        Cache::forget('dashboard.stats');

        return redirect()->route('assets.index')->with('success', 'Asset deleted.');
    }

    private function form(Asset $asset): View
    {
        return view('assets.form', [
            'asset' => $asset,
            'companies' => Company::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
