<?php

namespace Tests\Unit;

use App\Services\RecaptchaService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RecaptchaServiceTest extends TestCase
{
    public function test_verify_returns_true_in_dev_mode_when_no_secret_configured(): void
    {
        config([
            'privacy.recaptcha_secret_key' => '',
            'services.recaptcha.secret'   => '',
        ]);

        $service = new RecaptchaService();
        $this->assertTrue($service->verify(null, 'test'));
        $this->assertTrue($service->verify('dummy_token', 'test'));
    }

    public function test_verify_returns_false_when_token_is_empty_and_secret_is_configured(): void
    {
        config(['privacy.recaptcha_secret_key' => 'secret_key_123']);

        $service = new RecaptchaService();
        $this->assertFalse($service->verify(null, 'test'));
        $this->assertFalse($service->verify('', 'test'));
    }

    public function test_verify_passes_when_google_returns_success_and_high_score(): void
    {
        config([
            'privacy.recaptcha_secret_key' => 'secret_key_123',
            'privacy.recaptcha_score'      => 0.5,
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score'   => 0.9,
                'action'  => 'enquiry',
            ], 200),
        ]);

        $service = new RecaptchaService();
        $this->assertTrue($service->verify('valid_token', 'enquiry'));
    }

    public function test_verify_fails_when_score_is_below_min_threshold(): void
    {
        config([
            'privacy.recaptcha_secret_key' => 'secret_key_123',
            'privacy.recaptcha_score'      => 0.5,
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score'   => 0.2, // bot score
                'action'  => 'enquiry',
            ], 200),
        ]);

        $service = new RecaptchaService();
        $this->assertFalse($service->verify('bot_token', 'enquiry'));
    }

    public function test_verify_fails_open_on_google_http_error(): void
    {
        config(['privacy.recaptcha_secret_key' => 'secret_key_123']);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([], 500),
        ]);

        $service = new RecaptchaService();
        $this->assertTrue($service->verify('valid_token', 'enquiry'), 'Should fail-open on transient network/Google server error');
    }
}
