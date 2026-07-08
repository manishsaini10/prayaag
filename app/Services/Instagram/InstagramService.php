<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use App\Models\InstagramAccount;
use App\Models\InstagramSyncLog;
use App\Services\Instagram\DTOs\SyncResultDTO;
use Illuminate\Support\Facades\Log;

final class InstagramService
{
    public function __construct(
        public readonly FacebookOAuthService $oauth,
        public readonly TokenService $token,
        public readonly MediaSyncService $sync,
        public readonly FeedCacheService $cache,
    ) {}

    public function connect(string $code, string $state): InstagramAccount
    {
        $dto = $this->oauth->handleCallback($code, $state);

        // Disconnect any existing account with same business ID
        InstagramAccount::where('instagram_business_id', $dto->instagramBusinessId)
            ->update(['status' => 'disconnected']);

        $account = InstagramAccount::create([
            'facebook_page_id'      => $dto->facebookPageId,
            'instagram_business_id' => $dto->instagramBusinessId,
            'username'              => $dto->username,
            'name'                  => $dto->name,
            'profile_picture'       => $dto->profilePicture,
            'followers'             => $dto->followers,
            'media_count'           => $dto->mediaCount,
            'token'                 => $dto->token,
            'refresh_token'         => $dto->refreshToken,
            'expires_at'            => $dto->expiresAt,
            'status'                => 'active',
        ]);

        // Log the connection
        InstagramSyncLog::create([
            'account_id' => $account->id,
            'status'     => 'connected',
            'message'    => "Instagram account @{$dto->username} connected successfully",
        ]);

        return $account;
    }

    public function sync(string $accountId): SyncResultDTO
    {
        $account = InstagramAccount::connected()->findOrFail($accountId);
        return $this->sync->syncAccount($account);
    }

    public function syncAll(): array
    {
        $accounts = InstagramAccount::connected()->get();
        $results = [];

        foreach ($accounts as $account) {
            try {
                $results[] = $this->sync->syncAccount($account);
            } catch (\Throwable $e) {
                Log::channel(config('instagram.log_channel'))->error(
                    "Sync all failed for {$account->username}: {$e->getMessage()}"
                );
                $results[] = new SyncResultDTO(
                    accountId: $account->id,
                    status: 'failed',
                    message: $e->getMessage(),
                );
            }
        }

        return $results;
    }

    public function refreshTokens(): array
    {
        $accounts = InstagramAccount::connected()->get();
        $results = [];

        foreach ($accounts as $account) {
            try {
                $result = $this->token->refresh($account->token);
                $account->update([
                    'token'      => $result['token'],
                    'expires_at' => now()->addSeconds((int) $result['expires_in']),
                ]);

                InstagramSyncLog::create([
                    'account_id' => $account->id,
                    'status'     => 'token_refreshed',
                    'message'    => "Token refreshed for @{$account->username}",
                ]);

                $results[] = ['account' => $account->username, 'status' => 'refreshed'];
            } catch (\Throwable $e) {
                Log::channel(config('instagram.log_channel'))->error(
                    "Token refresh failed for {$account->username}: {$e->getMessage()}"
                );
                $results[] = ['account' => $account->username, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    public function disconnect(string $accountId): void
    {
        $account = InstagramAccount::findOrFail($accountId);
        $username = $account->username;

        $account->update(['status' => 'disconnected']);

        InstagramSyncLog::create([
            'account_id' => $account->id,
            'status'     => 'disconnected',
            'message'    => "Account @{$username} disconnected",
        ]);

        $this->cache->flush($accountId);
    }

    public function getDashboardStats(): array
    {
        $accounts = InstagramAccount::connected()->get();
        $expiringAccounts = InstagramAccount::connected()->expiringSoon(10)->get();

        return [
            'total_accounts'     => $accounts->count(),
            'total_followers'    => $accounts->sum('followers'),
            'total_media'        => \App\Models\InstagramMedia::count(),
            'latest_sync'        => $accounts->max('last_sync'),
            'expiring_tokens'    => $expiringAccounts,
            'connected_accounts' => $accounts,
            'recent_logs'        => InstagramSyncLog::latest()->limit(10)->get(),
        ];
    }
}
