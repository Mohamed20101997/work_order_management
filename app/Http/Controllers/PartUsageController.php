<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Part;
use App\Models\PartUsage;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class PartUsageController extends Controller
{
    public function store(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        Gate::authorize('parts.use');

        $data = $request->validate([
            'part_id' => ['required', 'exists:parts,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data, $workOrder, $request): void {
            $part = Part::whereKey($data['part_id'])->lockForUpdate()->firstOrFail();

            if ($part->quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Insufficient stock for {$part->name}. Available: {$part->quantity} {$part->unit}.",
                ]);
            }

            $part->decrement('quantity', $data['quantity']);

            PartUsage::create([
                'part_id' => $part->id,
                'work_order_id' => $workOrder->id,
                'user_id' => $request->user()->id,
                'quantity' => $data['quantity'],
                'used_at' => now(),
            ]);

            Activity::record($workOrder, 'part_used', "{$data['quantity']} x {$part->name} used on {$workOrder->number}");
        });

        return back()->with('success', 'Part usage recorded.');
    }
}
