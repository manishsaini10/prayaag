<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use App\Models\InstagramAccount;
use App\Models\InstagramMedia;
use App\Models\InstagramSyncLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * InstagramOEmbedService
 *
 * Uses Meta's free tokenless Instagram oEmbed API — No App ID, No Secret required!
 * Allows administrators to paste Instagram post/reel URLs to embed live posts on the website.
 *
 * oEmbed Endpoint: https://graph.facebook.com/v23.0/instagram_oembed?url={URL}
 * - No access token needed (made tokenless by Meta in June 2026)
 * - Rate limit: 1,000 requests/hour (cached aggressively for 24 hours)
 * - Compatible with all public posts and reels
 */
final class InstagramOEmbedService
{
    private const OEMBED_ENDPOINT = 'https://graph.facebook.com/v23.0/instagram_oembed';

    /**
     * Fetch oEmbed data for a single Instagram post URL.
     * Cached for 24 hours to prevent rate limit issues.
     */
    public function fetchEmbed(string $postUrl): ?array
    {
        $cacheKey = 'ig_oembed_' . md5($postUrl);

        return Cache::remember($cacheKey, 86400, function () use ($postUrl) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; PrayaagBot/1.0)'])
                    ->get(self::OEMBED_ENDPOINT, [
                        'url'        => $postUrl,
                        'maxwidth'   => 500,
                        'fields'     => 'html,thumbnail_url,author_name,provider_name,type,width,height',
                        'omitscript' => false,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Instagram oEmbed success: ' . $postUrl, $data);
                    return $data;
                }

                Log::warning('Instagram oEmbed HTTP ' . $response->status() . ' for: ' . $postUrl);
                return null;
            } catch (\Throwable $e) {
                Log::warning('Instagram oEmbed error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Save a list of Instagram post URLs into DB as embedded posts.
     * Called from admin "Add Post URLs" form — no API key needed.
     */
    public function savePostUrls(InstagramAccount $account, array $postUrls): array
    {
        $saved   = 0;
        $failed  = 0;
        $results = [];

        foreach ($postUrls as $rawUrl) {
            $url = trim($rawUrl);
            if (empty($url) || !str_contains($url, 'instagram.com')) {
                continue;
            }
            if (!str_ends_with($url, '/')) {
                $url .= '/';
            }

            $shortcode = $this->extractShortcode($url);
            if (!$shortcode) {
                $failed++;
                $results[] = ['url' => $url, 'status' => 'failed', 'reason' => 'Invalid Instagram URL'];
                continue;
            }

            $mediaId = 'oembed_' . $shortcode;

            // Try oEmbed for real thumbnail
            $embed = $this->fetchEmbed($url);
            $thumb = $embed['thumbnail_url'] ?? null;

            // Fallback thumbnail from Instagram CDN (works if account is public)
            if (!$thumb) {
                $thumb = "https://www.instagram.com/p/{$shortcode}/media/?size=m";
            }

            $isReel    = str_contains($url, '/reel/') || str_contains($url, '/tv/');
            $mediaType = $isReel ? 'VIDEO' : 'IMAGE';

            $payload = [
                'instagram_account_id' => $account->id,
                'media_id'             => $mediaId,
                'caption'              => $embed['author_name'] ?? '@' . $account->username,
                'media_type'           => $mediaType,
                'media_url'            => $thumb,
                'thumbnail_url'        => $thumb,
                'permalink'            => $url,
                'posted_at'            => now(),
                'likes'                => 0,
                'comments'             => 0,
                'is_cached'            => true,
                'raw'                  => [
                    'embed_html'   => $embed['html'] ?? null,
                    'shortcode'    => $shortcode,
                    'oembed'       => $embed,
                    'source'       => 'oembed',
                ],
            ];

            $existing = InstagramMedia::where('media_id', $mediaId)->first();
            if ($existing) {
                $existing->update($payload);
            } else {
                InstagramMedia::create(array_merge($payload, ['id' => (string) Str::ulid()]));
            }

            $saved++;
            $results[] = ['url' => $url, 'status' => 'saved', 'thumb' => $thumb, 'embed' => !empty($embed['html'])];
        }

        // Update account
        $account->update(['media_count' => $account->media()->count(), 'last_sync' => now()]);

        // Flush feed cache
        Cache::forget('ig_feed_v1');
        Cache::forget('ig_feed_count');

        // Log
        InstagramSyncLog::create([
            'account_id' => $account->id,
            'status'     => $saved > 0 ? 'success' : 'failed',
            'message'    => "oEmbed sync: {$saved} posts saved, {$failed} failed.",
        ]);

        return ['saved' => $saved, 'failed' => $failed, 'results' => $results];
    }

    /**
     * Extract Instagram post shortcode from URL
     */
    public function extractShortcode(string $url): ?string
    {
        if (preg_match('|instagram\.com/(?:p|reel|tv)/([A-Za-z0-9_\-]+)|', $url, $m)) {
            return $m[1];
        }
        return null;
    }
}
