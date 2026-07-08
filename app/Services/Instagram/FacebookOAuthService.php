<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use App\Models\InstagramAccount;
use App\Services\Instagram\DTOs\AccountDTO;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

final class FacebookOAuthService
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly GraphApiService $api,
    ) {}

    public function authorizationUrl(): string
    {
        $appId = config('instagram.app_id');
        $redirectUri = url(config('instagram.redirect_uri'));
        $scopes = implode(',', config('instagram.scopes', []));
        $state = Crypt::encryptString(session()->getId());

        session()->put('instagram_oauth_state', $state);

        return "https://www.facebook.com/" . config('instagram.graph_version', 'v23.0') . "/dialog/oauth" .
            "?client_id={$appId}" .
            "&redirect_uri={$redirectUri}" .
            "&scope={$scopes}" .
            "&state={$state}" .
            "&response_type=code";
    }

    public function handleCallback(string $code, string $state): AccountDTO
    {
        $expectedState = session()->pull('instagram_oauth_state');
        if ($state !== $expectedState) {
            throw new \RuntimeException('OAuth state mismatch — possible CSRF attack.');
        }

        $appId = config('instagram.app_id');
        $appSecret = config('instagram.app_secret');
        $redirectUri = url(config('instagram.redirect_uri'));

        // Exchange code for short-lived token
        $tokenData = $this->api->post('oauth/access_token', [
            'client_id'     => $appId,
            'client_secret' => $appSecret,
            'code'          => $code,
            'redirect_uri'  => $redirectUri,
        ]);

        $shortToken = $tokenData['access_token'] ?? null;
        if (! $shortToken) {
            $error = $tokenData['error']['message'] ?? 'Token exchange failed';
            Log::channel(config('instagram.log_channel'))->error("OAuth token exchange failed: {$error}");
            throw new \RuntimeException("Token exchange failed: {$error}");
        }

        // Exchange for long-lived token
        $longToken = $this->tokenService->exchangeLongLived($shortToken);
        $expiresAt = now()->addDays(60);

        // Get user's Facebook pages
        $pages = $this->getFacebookPages($longToken);

        $selectedPage = null;
        $igBusinessId = null;

        foreach ($pages as $page) {
            $pageId = $page['id'] ?? '';
            $pageToken = $page['access_token'] ?? $longToken;

            try {
                $igBiz = $this->getInstagramBusinessAccount($pageId, $pageToken);
                if ($igBiz) {
                    $selectedPage = $page;
                    $igBusinessId = $igBiz['id'];
                    break;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        if (! $selectedPage || ! $igBusinessId) {
            throw new \RuntimeException(
                'No Instagram Business Account found. ' .
                'Ensure your Instagram account is a Business/Creator account and connected to a Facebook Page.'
            );
        }

        // Get Instagram profile
        $profile = $this->getInstagramProfile($igBusinessId, $longToken);

        return new AccountDTO(
            facebookPageId: $selectedPage['id'],
            instagramBusinessId: $igBusinessId,
            username: $profile['username'] ?? '',
            name: $profile['name'] ?? $selectedPage['name'] ?? '',
            profilePicture: $profile['profile_picture_url'] ?? null,
            followers: (int) ($profile['followers_count'] ?? 0),
            mediaCount: (int) ($profile['media_count'] ?? 0),
            token: $longToken,
            refreshToken: null,
            expiresAt: $expiresAt,
        );
    }

    public function getFacebookPages(string $token): array
    {
        return $this->api->get('me/accounts', [
            'access_token' => $token,
            'limit' => 100,
            'fields' => 'id,name,access_token,category,instagram_business_account',
        ])['data'] ?? [];
    }

    public function getInstagramBusinessAccount(string $pageId, string $pageToken): ?array
    {
        try {
            $data = $this->api->get($pageId, [
                'fields' => 'instagram_business_account{id,username,name,profile_picture_url,followers_count,media_count}',
                'access_token' => $pageToken,
            ]);

            return $data['instagram_business_account'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function getInstagramProfile(string $igBusinessId, string $token): array
    {
        return $this->api->get($igBusinessId, [
            'fields' => 'id,username,name,profile_picture_url,followers_count,media_count',
            'access_token' => $token,
        ]);
    }
}
