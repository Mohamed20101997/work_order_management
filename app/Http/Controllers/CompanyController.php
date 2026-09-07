<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('companies.view');

        $companies = Company::withCount('assets')
            ->when($request->string('q')->toString(), fn ($query, $term) => $query->where('name', 'like', "%{$term}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        Gate::authorize('companies.manage');

        return view('companies.form', ['company' => new Company()]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        Gate::authorize('companies.manage');

        $company = Company::create($request->validated());

        return redirect()->route('companies.show', $company)->with('success', 'Company created.');
    }

    public function show(Company $company): View
    {
        Gate::authorize('companies.view');

        $company->load(['assets' => fn ($query) => $query->with('assetType')->latest('received_date')->limit(20)]);

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        Gate::authorize('companies.manage');

        return view('companies.form', compact('company'));
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        Gate::authorize('companies.manage');

        $company->update($request->validated());

        return redirect()->route('companies.show', $company)->with('success', 'Company updated.');
    }
}
