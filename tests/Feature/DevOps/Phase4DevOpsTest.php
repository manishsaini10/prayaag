<?php

namespace Tests\Feature\DevOps;

use App\Services\SentryReporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Phase 4 — DevOps, Reliability & Error Handling Feature Tests.
 *
 * Tests:
 *  1. SentryReporter PII data sanitization.
 *  2. API standardized JSON error response envelope.
 *  3. System Health probes including Sentry monitoring check.
 */
class Phase4DevOpsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sentry_reporter_sanitizes_pii_fields(): void
    {
        $reporter = app(SentryReporter::class);

        $input = [
            'name'                 => 'John Doe',
            'password'             => 'Secret123!',
            'g-recaptcha-response' => 'token_xyz',
            'credit_card'          => '4111222233334444',
            'nested'               => [
                'password' => 'NestedSecret',
                'email'    => 'john@example.com',
            ],
        ];

        $sanitized = $reporter->sanitize($input);

        $this->assertSame('[REDACTED]', $sanitized['password']);
        $this->assertSame('[REDACTED]', $sanitized['g-recaptcha-response']);
        $this->assertSame('[REDACTED]', $sanitized['credit_card']);
        $this->assertSame('[REDACTED]', $sanitized['nested']['password']);
        $this->assertSame('john@example.com', $sanitized['nested']['email']);
        $this->assertSame('John Doe', $sanitized['name']);
    }

    public function test_api_404_returns_standardized_json_error_envelope(): void
    {
        $response = $this->getJson('/api/non-existent-endpoint');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error'   => [
                'code' => 'NOT_FOUND',
            ],
        ]);
    }

    public function test_system_health_includes_sentry_monitoring_probe(): void
    {
        $admin = \App\Models\User::factory()->create(['two_factor_enabled' => true]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
                         ->withSession(['2fa_passed' => true])
                         ->get('/admin/system-health');

        $response->assertStatus(200);
        $response->assertSee('Sentry Monitoring');
    }
}
