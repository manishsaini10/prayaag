<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function index()
    {
        Gate::authorize('chatbot.contacts.view');

        $companies = Company::withCount('contacts')
            ->orderBy('name')
            ->paginate(20);

        return view('chatbot.admin.companies.index', compact('companies'));
    }

    public function create()
    {
        Gate::authorize('chatbot.contacts.create');
        return view('chatbot.admin.companies.index', ['companies' => Company::withCount('contacts')->orderBy('name')->paginate(20), 'showCreateModal' => true]);
    }

    public function edit(Company $company)
    {
        Gate::authorize('chatbot.contacts.update');
        return view('chatbot.admin.companies.show', compact('company'));
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.contacts.create');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Company::create($data);

        return back()->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        Gate::authorize('chatbot.contacts.view');

        $company->load(['contacts', 'deals.pipeline', 'deals.stage']);

        return view('chatbot.admin.companies.show', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        Gate::authorize('chatbot.contacts.update');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $company->update($data);

        return back()->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        Gate::authorize('chatbot.contacts.delete');

        $company->delete();

        return redirect()->route('admin.chatbot.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
