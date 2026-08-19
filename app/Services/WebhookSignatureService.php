<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * WebhookSignatureService — HMAC-SHA256 signature verification for incoming
 * and outgoing webhooks.
 *
 * Prevents tampering and unauthorized webhook payloads.
 */
class WebhookSignatureService
{
    /**
     * Generate HMAC-SHA256 signature for outgoing payload.
     */
    public function sign(string $payload, string $secret): string
    {
        return 'sha256=' . hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Verify incoming webhook signature against secret.
     * Uses hash_equals for timing-attack protection.
     */
    public function verify(string $payload, string $signature, string $secret): bool
    {
        if (empty($signature) || empty($secret)) {
            return false;
        }

        // Support both "sha256=hash" header format and raw hash
        $expected = $this->sign($payload, $secret);
        $provided = str_starts_with($signature, 'sha256=') ? $signature : 'sha256=' . $signature;

        $isValid = hash_equals($expected, $provided);

        if (! $isValid) {
            Log::warning('Webhook signature verification failed', [
                'provided_length' => strlen($provided),
            ]);
        }

        return $isValid;
    }
}
