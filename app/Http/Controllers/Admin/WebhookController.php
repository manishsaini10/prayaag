<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Webhook;
use App\Models\Chatbot\Enterprise\WebhookLog;
use App\Core\Webhook\WebhookDispatcher;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index()
    {
        $webhooks = Webhook::latest()->paginate(20);
        return view('chatbot.admin.webhooks.index', compact('webhooks'));
    }

    public function create()
    {
        return view('chatbot.admin.webhooks.form', ['webhook' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array',
            'events.*' => 'string',
            'method' => 'nullable|string|in:POST,PUT,PATCH',
            'secret' => 'nullable|string|max:255',
            'timeout_seconds' => 'nullable|integer|min:1|max:120',
            'retry_count' => 'nullable|integer|min:0|max:10',
            'headers' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['headers'] = $validated['headers'] ?? [];

        $webhook = Webhook::create($validated);

        return redirect()->route('admin.chatbot.webhooks.index')
            ->with('success', 'Webhook created successfully.');
    }

    public function show($id)
    {
        $webhook = Webhook::with('logs')->findOrFail($id);
        return view('chatbot.admin.webhooks.show', compact('webhook'));
    }

    public function edit($id)
    {
        $webhook = Webhook::findOrFail($id);
        return view('chatbot.admin.webhooks.form', compact('webhook'));
    }

    public function update(Request $request, $id)
    {
        $webhook = Webhook::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array',
            'events.*' => 'string',
            'method' => 'nullable|string|in:POST,PUT,PATCH',
            'secret' => 'nullable|string|max:255',
            'timeout_seconds' => 'nullable|integer|min:1|max:120',
            'retry_count' => 'nullable|integer|min:0|max:10',
            'headers' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['headers'] = $validated['headers'] ?? [];
        $webhook->update($validated);

        return redirect()->route('admin.chatbot.webhooks.index')
            ->with('success', 'Webhook updated successfully.');
    }

    public function destroy($id)
    {
        $webhook = Webhook::findOrFail($id);
        $webhook->delete();

        return redirect()->route('admin.chatbot.webhooks.index')
            ->with('success', 'Webhook deleted successfully.');
    }

    public function test($id, WebhookDispatcher $dispatcher)
    {
        $webhook = Webhook::findOrFail($id);
        $dispatcher->dispatch('test', [
            'message' => 'This is a test webhook from ' . config('app.name'),
            'timestamp' => now()->toIso8601String(),
        ]);

        return redirect()->route('admin.chatbot.webhooks.show', $id)
            ->with('success', 'Test webhook dispatched. Check the delivery log below.');
    }

    public function logs()
    {
        $logs = WebhookLog::with('webhook')->latest()->paginate(50);
        return view('chatbot.admin.webhooks.index', compact('logs'));
    }

    public function showLog($id)
    {
        $log = WebhookLog::with('webhook')->findOrFail($id);
        return view('chatbot.admin.webhooks.show', compact('log'));
    }
}
