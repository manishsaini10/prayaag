<?php

namespace App\Core\Webhook;

use App\Models\Chatbot\Enterprise\Webhook;
use App\Models\Chatbot\Enterprise\WebhookLog;
use Illuminate\Support\Facades\Http;

class WebhookDispatcher
{
    public function dispatch(string $event, array $payload = []): void
    {
        $webhooks = Webhook::where('status', 'active')
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($webhooks as $webhook) {
            try {
                $startTime = microtime(true);

                $response = Http::timeout($webhook->timeout_seconds ?: 15)
                    ->withHeaders(array_merge(
                        $webhook->headers ?? [],
                        ['Content-Type' => 'application/json']
                    ))
                    ->withOptions(['verify' => false])
                    ->send($webhook->method ?: 'POST', $webhook->url, [
                        'json' => [
                            'event' => $event,
                            'data' => $payload,
                            'timestamp' => now()->toIso8601String(),
                        ],
                    ]);

                $duration = (int) ((microtime(true) - $startTime) * 1000);

                WebhookLog::create([
                    'webhook_id' => $webhook->id,
                    'event' => $event,
                    'payload' => $payload,
                    'response_status' => $response->status(),
                    'response_body' => substr($response->body(), 0, 5000),
                    'duration_ms' => $duration,
                    'status' => $response->successful() ? 'success' : 'failed',
                ]);
            } catch (\Exception $e) {
                WebhookLog::create([
                    'webhook_id' => $webhook->id,
                    'event' => $event,
                    'payload' => $payload,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
}
