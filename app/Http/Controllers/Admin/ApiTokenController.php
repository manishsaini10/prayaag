<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\ApiToken;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::latest()->paginate(20);
        return view('admin.api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'rate_limit' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $plainText = \Illuminate\Support\Str::random(64);
        $token = ApiToken::create([
            'name' => $validated['name'],
            'token' => hash('sha256', $plainText),
            'permissions' => $validated['permissions'] ?? ['read'],
            'rate_limit' => $validated['rate_limit'] ?? 60,
            'expires_at' => $validated['expires_at'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.api-tokens.index')
            ->with('token', $plainText)
            ->with('success', 'API token created. Copy it now — it won\'t be shown again.');
    }

    public function destroy($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->delete();

        return redirect()->route('admin.api-tokens.index')
            ->with('success', 'API token revoked successfully.');
    }

    public function revoke($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->update(['revoked_at' => now()]);

        return redirect()->route('admin.api-tokens.index')
            ->with('success', 'API token revoked successfully.');
    }
}
