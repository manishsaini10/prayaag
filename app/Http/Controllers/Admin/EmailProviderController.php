<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailProviderConfig;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailProviderController extends Controller
{
    public function index(): View
    {
        $providers = EmailProviderConfig::ordered()->get();
        return view('admin.email-providers.index', compact('providers'));
    }

    public function create(): View
    {
        return view('admin.email-providers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_key' => 'required|string',
            'label' => 'required|string|max:255',
            'credentials' => 'required|array',
        ]);

        $count = EmailProviderConfig::count();

        $config = EmailProviderConfig::create([
            'provider_key' => $validated['provider_key'],
            'label' => $validated['label'],
            'credentials' => $validated['credentials'],
            'is_active' => ($count === 0), // First created is active by default
            'priority_order' => $count + 1,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.email-providers.index')
            ->with('success', "Provider '{$config->label}' created successfully.");
    }

    public function edit(string $id): View
    {
        $provider = EmailProviderConfig::findOrFail($id);
        return view('admin.email-providers.edit', compact('provider'));
    }

    public function update(Request $request, string $id)
    {
        $provider = EmailProviderConfig::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'credentials' => 'required|array',
        ]);

        // Merge credentials so unedited masked fields aren't wiped
        $existing = $provider->credentials ?? [];
        $merged = array_merge($existing, array_filter($validated['credentials'], fn($v) => !empty($v)));

        $provider->update([
            'label' => $validated['label'],
            'credentials' => $merged,
        ]);

        return redirect()->route('admin.email-providers.index')
            ->with('success', "Provider '{$provider->label}' updated successfully.");
    }

    public function destroy(string $id)
    {
        $provider = EmailProviderConfig::findOrFail($id);
        if ($provider->is_active) {
            return back()->with('error', 'Cannot delete the active email provider. Please set another provider as active first.');
        }

        $provider->delete();
        return redirect()->route('admin.email-providers.index')->with('success', 'Provider deleted.');
    }

    public function testConnection(Request $request, string $id)
    {
        $provider = EmailProviderConfig::findOrFail($id);
        $instance = $provider->getProviderInstance();

        $adminEmail = auth()->user()->email ?? config('mail.from.address', 'admin@example.com');

        $result = $instance->testConnection(array_merge($provider->credentials ?? [], [
            'from_email' => $adminEmail,
        ]));

        $provider->update([
            'is_verified' => $result->success,
            'last_tested_at' => now(),
            'failure_count' => $result->success ? 0 : ($provider->failure_count + 1),
        ]);

        if ($result->success) {
            return response()->json(['success' => true, 'message' => 'Test email sent successfully! Check your inbox.']);
        }

        return response()->json(['success' => false, 'message' => 'Connection test failed: ' . $result->error], 422);
    }

    public function setActive(string $id)
    {
        EmailProviderConfig::query()->update(['is_active' => false]);
        $provider = EmailProviderConfig::findOrFail($id);
        $provider->update(['is_active' => true]);

        return back()->with('success', "'{$provider->label}' set as active email provider.");
    }
}
