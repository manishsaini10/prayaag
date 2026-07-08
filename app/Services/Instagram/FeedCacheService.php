<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use App\Models\InstagramAccount;
use App\Models\InstagramMedia;
use Illuminate\Support\Facades\Cache;

final class FeedCacheService
{
    public function getFeed(?string $accountId = null, int $limit = 12, int $offset = 0, ?string $type = null): array
    {
        $cacheKey = $this->cacheKey($accountId, $limit, $offset, $type);
        $duration = (int) config('instagram.cache_duration', 3600);

        return Cache::remember($cacheKey, $duration, function () use ($accountId, $limit, $offset, $type) {
            return $this->queryFeed($accountId, $limit, $offset, $type);
        });
    }

    public function getProfile(?string $accountId = null): ?array
    {
        $account = $accountId
            ? InstagramAccount::connected()->where('id', $accountId)->first()
            : InstagramAccount::connected()->first();

        if (! $account) return null;

        return [
            'id'              => $account->id,
            'username'        => $account->username,
            'name'            => $account->name,
            'profile_picture' => $account->profile_picture,
            'followers'       => $account->followers,
            'media_count'     => $account->media()->count(),
        ];
    }

    public function getStats(?string $accountId = null): array
    {
        $query = InstagramAccount::connected();
        if ($accountId) $query->where('id', $accountId);

        $accounts = $query->get();
        $totalMedia = InstagramMedia::count();

        return [
            'accounts'      => $accounts->count(),
            'total_followers' => $accounts->sum('followers'),
            'total_media'   => $totalMedia,
            'images'        => InstagramMedia::images()->count(),
            'videos'        => InstagramMedia::videos()->count(),
        ];
    }

    public function flush(string $accountId): void
    {
        $pattern = "instagram:feed:{$accountId}:*";
        // Can't delete by pattern in all drivers; flush by prefix
        Cache::flush(); // Simplified — in production use tagged cache
    }

    private function queryFeed(?string $accountId, int $limit, int $offset, ?string $type): array
    {
        $query = InstagramMedia::published()->latest('posted_at');

        if ($accountId) {
            $query->where('instagram_account_id', $accountId);
        }

        if ($type && $type !== 'all') {
            $query->where('media_type', strtoupper($type));
        }

        $total = (clone $query)->count();
        $items = (clone $query)->skip($offset)->limit($limit)->get();

        return [
            'data' => $items->map(fn(InstagramMedia $m) => [
                'id'            => $m->id,
                'media_id'      => $m->media_id,
                'caption'       => $m->caption,
                'media_type'    => $m->media_type,
                'media_url'     => $m->media_url,
                'thumbnail_url' => $m->thumbnail_url,
                'permalink'     => $m->permalink,
                'timestamp'     => $m->posted_at?->toIso8601String(),
                'likes'         => $m->likes,
                'comments'      => $m->comments,
                'is_cached'     => $m->is_cached,
            ])->toArray(),
            'total'    => $total,
            'has_more' => ($offset + $limit) < $total,
        ];
    }

    private function cacheKey(?string $accountId, int $limit, int $offset, ?string $type): string
    {
        return "instagram:feed:{$accountId}:{$limit}:{$offset}:{$type}";
    }
}
