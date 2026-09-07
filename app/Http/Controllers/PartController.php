<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartRequest;
use App\Models\Part;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PartController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('parts.view');

        $parts = Part::query()
            ->when($request->string('q')->toString(), fn ($query, $term) => $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', "%{$term}%")->orWhere('part_number', 'like', "%{$term}%");
            }))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('parts.index', compact('parts'));
    }

    public function create(): View
    {
        Gate::authorize('parts.manage');

        return view('parts.form', ['part' => new Part()]);
    }

    public function store(PartRequest $request): RedirectResponse
    {
        Gate::authorize('parts.manage');

        Part::create($request->validated());

        return redirect()->route('parts.index')->with('success', 'Part created.');
    }

    public function edit(Part $part): View
    {
        Gate::authorize('parts.manage');

        return view('parts.form', compact('part'));
    }

    public function update(PartRequest $request, Part $part): RedirectResponse
    {
        Gate::authorize('parts.manage');

        $part->update($request->validated());

        return redirect()->route('parts.index')->with('success', 'Part updated.');
    }
}
