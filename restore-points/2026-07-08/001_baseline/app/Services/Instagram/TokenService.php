<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use Illuminate\Support\Facades\Log;

final class TokenService
{
    public function __construct(
        private readonly GraphApiService $api,
    ) {}

    public function exchangeLongLived(string $shortToken): string
    {
        $data = $this->api->get('oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => config('instagram.app_id'),
            'client_secret'     => config('instagram.app_secret'),
            'fb_exchange_token' => $shortToken,
        ]);

        $token = $data['access_token'] ?? null;
        if (! $token) {
            throw new \RuntimeException('Long-lived token exchange failed: no access_token in response');
        }

        return $token;
    }

    public function refresh(string $token): array
    {
        $data = $this->api->get('oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => config('instagram.app_id'),
            'client_secret'     => config('instagram.app_secret'),
            'fb_exchange_token' => $token,
        ]);

        $newToken = $data['access_token'] ?? null;
        if (! $newToken) {
            throw new \RuntimeException('Token refresh failed: no access_token in response');
        }

        return [
            'token'      => $newToken,
            'expires_in' => $data['expires_in'] ?? 5184000,
        ];
    }

    public function validate(string $token): bool
    {
        try {
            $data = $this->api->get('debug_token', [
                'input_token'  => $token,
                'access_token' => config('instagram.app_id') . '|' . config('instagram.app_secret'),
            ]);

            return $data['data']['is_valid'] ?? false;
        } catch (\Throwable) {
            return false;
        }
    }

    public function getExpiry(string $token): ?\DateTimeInterface
    {
        try {
            $data = $this->api->get('debug_token', [
                'input_token'  => $token,
                'access_token' => config('instagram.app_id') . '|' . config('instagram.app_secret'),
            ]);

            $expiresAt = $data['data']['expires_at'] ?? null;
            return $expiresAt ? now()->addTimestamp($expiresAt) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
