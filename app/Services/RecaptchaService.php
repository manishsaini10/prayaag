<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class RecaptchaService
{
    /**
     * Verify the Google reCAPTCHA token.
     *
     * @param string $token The token from the client (g-recaptcha-response)
     * @return bool True if verification succeeds
     */
    public static function verify(string $token): bool
    {
        $secret = config('services.recaptcha.secret');
        if (!$secret) {
            // If secret not configured, consider it a failure to avoid insecure bypass.
            return false;
        }
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $token,
            ]);
            $data = $response->json();
            return $data['success'] ?? false;
        } catch (Exception $e) {
            return false;
        }
    }
}
