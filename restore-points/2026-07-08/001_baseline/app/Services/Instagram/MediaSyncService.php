<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use App\Models\InstagramAccount;
use App\Models\InstagramMedia;
use App\Models\InstagramSyncLog;
use App\Services\Instagram\DTOs\MediaDTO;
use App\Services\Instagram\DTOs\SyncResultDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class MediaSyncService
{
    public function __construct(
        private readonly GraphApiService $api,
    ) {}

    public function syncAccount(InstagramAccount $account): SyncResultDTO
    {
        $startTime = microtime(true);
        $created = 0;
        $updated = 0;
        $failed = 0;

        try {
            $token = $account->token;
            if (! $token) {
                throw new \RuntimeException('No access token available');
            }

            $igBizId = $account->instagram_business_id;
            $version = config('instagram.graph_version', 'v23.0');

            // Fetch media in pages
            $fields = 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,like_count,comments_count,children{id,media_type,media_url,thumbnail_url}';
            $endpoint = "{$igBizId}/media?fields={$fields}&access_token={$token}&limit=100";

            $hasMore = true;
            $page = 0;

            while ($hasMore && $page < 10) {
                $page++;
                $responseData = $this->api->get($endpoint);

                $data = $responseData['data'] ?? [];
                $errors = [];

                foreach ($data as $item) {
                    try {
                        $dto = MediaDTO::fromGraphApi($item);
                        $result = $this->upsertMedia($account->id, $dto);
                        if ($result === 'created') $created++;
                        elseif ($result === 'updated') $updated++;
                        else $failed++;
                    } catch (\Throwable $e) {
                        $failed++;
                        $errors[] = $e->getMessage();
                        Log::channel(config('instagram.log_channel'))->warning(
                            "Media sync failed for item {$item['id']}: " . $e->getMessage()
                        );
                    }
                }

                // Process children (carousel albums)
                foreach ($data as $item) {
                    if (($item['media_type'] ?? '') === 'CAROUSEL_ALBUM' && ! empty($item['children']['data'])) {
                        foreach ($item['children']['data'] as $child) {
                            try {
                                $childDto = MediaDTO::fromGraphApi($child);
                                $result = $this->upsertMedia($account->id, $childDto);
                                if ($result === 'created') $created++;
                                elseif ($result === 'updated') $updated++;
                            } catch (\Throwable) {
                                $failed++;
                            }
                        }
                    }
                }

                $url = $responseData['paging']['next'] ?? null;
                $hasMore = $url !== null;
            }

            // Update profile sync
            $this->updateProfile($account, $token, $igBizId);

            $executionTime = round(microtime(true) - $startTime, 2);
            $status = 'success';
            $message = "Synced {$created} new, {$updated} updated, {$failed} failed";

        } catch (\Throwable $e) {
            $executionTime = round(microtime(true) - $startTime, 2);
            $status = 'failed';
            $message = $e->getMessage();
            Log::channel(config('instagram.log_channel'))->error(
                "Media sync failed for account {$account->username}: {$message}"
            );
        }

        $account->update(['last_sync' => now()]);

        // Log sync result
        InstagramSyncLog::create([
            'account_id'     => $account->id,
            'status'         => $status,
            'message'        => $message,
            'execution_time' => $executionTime,
        ]);

        return new SyncResultDTO(
            accountId: $account->id,
            status: $status,
            message: $message,
            created: $created,
            updated: $updated,
            failed: $failed,
            executionTime: $executionTime,
        );
    }

    private function upsertMedia(string $accountId, MediaDTO $dto): string
    {
        $existing = InstagramMedia::where('media_id', $dto->mediaId)->first();

        $data = [
            'instagram_account_id' => $accountId,
            'caption'              => $dto->caption,
            'media_type'           => $dto->mediaType,
            'media_url'            => $dto->mediaUrl,
            'thumbnail_url'        => $dto->thumbnailUrl,
            'permalink'            => $dto->permalink,
            'posted_at'            => $dto->timestamp,
            'children'             => $dto->children,
            'likes'                => $dto->likeCount,
            'comments'             => $dto->commentCount,
            'raw'                  => [
                'media_id'    => $dto->mediaId,
                'media_type'  => $dto->mediaType,
                'likes'       => $dto->likeCount,
                'comments'    => $dto->commentCount,
            ],
        ];

        // Download media for local cache
        if (config('instagram.enable_local_cache')) {
            $localPath = $this->downloadMedia($dto, $accountId);
            if ($localPath) {
                $data['media_url'] = $localPath;
                $data['is_cached'] = true;
            }
        }

        if ($existing) {
            $existing->update($data);
            return 'updated';
        }

        InstagramMedia::create(array_merge($data, ['media_id' => $dto->mediaId]));
        return 'created';
    }

    private function downloadMedia(MediaDTO $dto, string $accountId): ?string
    {
        $url = $dto->thumbnailUrl ?: $dto->mediaUrl;
        if (empty($url)) return null;

        $ext = match ($dto->mediaType) {
            'VIDEO', 'REEL' => 'mp4',
            default => 'jpg',
        };

        $filename = "{$accountId}/{$dto->mediaId}.{$ext}";
        $storagePath = "public/instagram/{$filename}";

        if (Storage::exists($storagePath)) {
            return Storage::url($storagePath);
        }

        try {
            $response = Http::timeout(30)->get($url);
            if ($response->failed()) return null;

            Storage::put($storagePath, $response->body());
            return Storage::url($storagePath);
        } catch (\Throwable $e) {
            Log::channel(config('instagram.log_channel'))->warning(
                "Media download failed: {$dto->mediaId} - {$e->getMessage()}"
            );
            return null;
        }
    }

    private function updateProfile(InstagramAccount $account, string $token, string $igBizId): void
    {
        $responseData = $this->api->get("{$igBizId}", [
            'fields'       => 'id,username,name,profile_picture_url,followers_count,media_count',
            'access_token' => $token,
        ]);

        if (! empty($responseData)) {
            $account->update([
                'username'         => $responseData['username'] ?? $account->username,
                'name'             => $responseData['name'] ?? $account->name,
                'profile_picture'  => $responseData['profile_picture_url'] ?? $account->profile_picture,
                'followers'        => (int) ($responseData['followers_count'] ?? $account->followers),
            ]);
        }
    }
}
