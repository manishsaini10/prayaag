<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google reCAPTCHA v3 server-side verification.
 *
 * Config: config/privacy.php  →  recaptcha_secret_key, recaptcha_score
 * Keys:   RECAPTCHA_SITE_KEY, RECAPTCHA_SECRET_KEY in .env
 *
 * Usage:
 *   app(RecaptchaService::class)->verify($request->input('g-recaptcha-response'), 'enquiry')
 *
 * - Returns true when no secret key is configured (dev/local bypass).
 * - Fail-open on transient HTTP errors (prefer UX over false rejection).
 * - Fail-close when token is missing or score is below threshold.
 */
class RecaptchaService
{
    protected string $secret;
    protected float  $minScore;

    public function __construct()
    {
        $settings = app(\App\Core\Settings\SettingsManager::class);
        $dbSecret = $settings->get('recaptcha_secret_key');

        $this->secret   = !empty($dbSecret) ? $dbSecret : (config('privacy.recaptcha_secret_key') ?: config('services.recaptcha.secret', ''));
        $this->minScore = (float) config('privacy.recaptcha_score', 0.5);
    }

    public function isEnabled(): bool
    {
        $settings = app(\App\Core\Settings\SettingsManager::class);
        return filter_var($settings->get('recaptcha_enabled', true), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Verify a reCAPTCHA token.
     *
     * @param  string|null  $token   The g-recaptcha-response value from the form.
     * @param  string       $action  The action name set in the JS widget (e.g. "enquiry").
     */
    public function verify(?string $token, string $action = 'submit'): bool
    {
        // Skip verification when reCAPTCHA is disabled in Admin Settings
        if (! $this->isEnabled()) {
            return true;
        }

        // Skip verification when keys are not configured (local/dev environment).
        if (empty($this->secret)) {
            return true;
        }

        if (empty($token)) {
            Log::info('reCAPTCHA: empty token rejected', ['action' => $action]);
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $this->secret,
                'response' => $token,
            ]);

            if (! $response->successful()) {
                Log::warning('reCAPTCHA HTTP error — failing open', [
                    'status' => $response->status(),
                    'action' => $action,
                ]);
                return true; // transient Google error — don't block real users
            }

            $data = $response->json();

            if (! ($data['success'] ?? false)) {
                Log::info('reCAPTCHA verification failed', [
                    'errors' => $data['error-codes'] ?? [],
                    'action' => $action,
                ]);
                return false;
            }

            $score = (float) ($data['score'] ?? 0.0);
            if ($score < $this->minScore) {
                Log::info('reCAPTCHA score too low — bot suspected', [
                    'score'  => $score,
                    'min'    => $this->minScore,
                    'action' => $action,
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            // Never block a legitimate user due to a transient network error.
            Log::error('reCAPTCHA service exception — failing open', ['error' => $e->getMessage()]);
            return true;
        }
    }

    /**
     * Backwards-compatible static alias (used by older controller code).
     */
    public static function verify_static(?string $token): bool
    {
        return app(self::class)->verify($token);
    }
}
