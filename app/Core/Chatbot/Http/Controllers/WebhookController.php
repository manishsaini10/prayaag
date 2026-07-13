<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Gate;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('chatbot.webhooks.view');
        $query = Webhook::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('event')) {
            $query->whereJsonContains('events', $request->event);
        }

        $webhooks = $query->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($webhooks);
        }

        return view('chatbot.admin.webhooks.index', compact('webhooks'));
    }

    public function create()
    {
        Gate::authorize('chatbot.webhooks.create');
        return view('chatbot.admin.webhooks.form');
    }

    public function store(Request $request)
    {
        Gate::authorize('chatbot.webhooks.create');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'secret' => 'nullable|string|max:255',
            'events' => 'required|array',
            'events.*' => 'string',
            'method' => 'nullable|string|in:GET,POST,PUT,PATCH,DELETE',
            'headers' => 'nullable|json',
            'retry_count' => 'nullable|integer|min:0|max:10',
            'timeout_seconds' => 'nullable|integer|min:1|max:60',
            'status' => 'nullable|string|in:active,inactive,disabled',
        ]);

        $data['events'] = json_encode($data['events']);
        $data['created_by'] = auth()->id();
        Webhook::create($data);

        return redirect()->route('admin.chatbot.webhooks.index')
            ->with('success', 'Webhook created successfully.');
    }

    public function show(Webhook $webhook)
    {
        Gate::authorize('chatbot.webhooks.view');
        $webhook->load('logs');

        if (request()->wantsJson()) {
            return response()->json($webhook);
        }

        return view('chatbot.admin.webhooks.show', compact('webhook'));
    }

    public function edit(Webhook $webhook)
    {
        Gate::authorize('chatbot.webhooks.update');
        return view('chatbot.admin.webhooks.form', compact('webhook'));
    }

    public function update(Request $request, Webhook $webhook)
    {
        Gate::authorize('chatbot.webhooks.update');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'secret' => 'nullable|string|max:255',
            'events' => 'required|array',
            'events.*' => 'string',
            'method' => 'nullable|string|in:GET,POST,PUT,PATCH,DELETE',
            'headers' => 'nullable|json',
            'retry_count' => 'nullable|integer|min:0|max:10',
            'timeout_seconds' => 'nullable|integer|min:1|max:60',
            'status' => 'nullable|string|in:active,inactive,disabled',
        ]);

        $data['events'] = json_encode($data['events']);
        $webhook->update($data);

        if ($request->wantsJson()) {
            return response()->json($webhook);
        }

        return redirect()->route('admin.chatbot.webhooks.index')
            ->with('success', 'Webhook updated successfully.');
    }

    public function destroy(Webhook $webhook)
    {
        Gate::authorize('chatbot.webhooks.delete');
        $webhook->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Webhook deleted successfully.']);
        }

        return redirect()->route('admin.chatbot.webhooks.index')
            ->with('success', 'Webhook deleted successfully.');
    }

    public function test(Webhook $webhook)
    {
        Gate::authorize('chatbot.webhooks.test');
        $payload = [
            'event' => 'test',
            'data' => [
                'message' => 'This is a test webhook payload.',
                'timestamp' => now()->toIso8601String(),
            ],
        ];

        try {
            $response = Http::timeout($webhook->timeout_seconds)
                ->withHeaders($webhook->headers ?? [])
                ->withOptions(['verify' => false])
                ->send($webhook->method, $webhook->url, [
                    'json' => $payload,
                ]);

            $status = $response->successful() ? 'success' : 'failed';

            $webhook->logs()->create([
                'event' => 'test',
                'payload' => $payload,
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 5000),
                'status' => $status,
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'status' => $status,
                    'response_status' => $response->status(),
                ]);
            }

            return back()->with(
                $status === 'success' ? 'success' : 'error',
                "Webhook test {$status} with status {$response->status()}."
            );
        } catch (\Exception $e) {
            $webhook->logs()->create([
                'event' => 'test',
                'payload' => $payload,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            if (request()->wantsJson()) {
                return response()->json(['status' => 'failed', 'error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Webhook test failed: ' . $e->getMessage());
        }
    }
}
