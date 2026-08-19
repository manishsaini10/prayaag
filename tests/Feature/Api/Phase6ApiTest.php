<?php

namespace Tests\Feature\Api;

use App\Services\WebhookSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 6 — API & Integration Architecture Feature Tests.
 *
 * Tests:
 *  1. V1 REST API endpoints response structure.
 *  2. WebhookSignatureService signing and verification.
 */
class Phase6ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_v1_mess_menu_api_returns_success_envelope(): void
    {
        $response = $this->getJson('/api/v1/mess-menu');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_v1_testimonials_api_returns_success_envelope(): void
    {
        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure(['success', 'data', 'meta']);
    }

    public function test_v1_jobs_api_returns_success_envelope(): void
    {
        $response = $this->getJson('/api/v1/jobs');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_webhook_signature_service_signs_and_verifies(): void
    {
        $service   = app(WebhookSignatureService::class);
        $payload   = '{"event":"testimonial.submitted","id":"123"}';
        $secret    = 'super_secret_webhook_key';

        $signature = $service->sign($payload, $secret);

        $this->assertNotEmpty($signature);
        $this->assertTrue($service->verify($payload, $signature, $secret));
        $this->assertFalse($service->verify($payload, 'sha256=invalid_hash', $secret));
    }
}
