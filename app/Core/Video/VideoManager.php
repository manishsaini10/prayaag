<?php

namespace App\Core\Video;

use App\Core\Video\Providers\VideoProviderInterface;
use App\Core\Video\Providers\YouTubeUnlistedProvider;
use App\Core\Video\Providers\CloudflareStreamProvider;
use App\Core\Video\Providers\LocalStorageProvider;
use App\Core\Video\Providers\InstagramReelProvider;

/**
 * VideoManager — facade-style provider resolver.
 *
 * Usage (anywhere in the app):
 *   app(VideoManager::class)->driver()->upload($source, $meta);
 *   app(VideoManager::class)->driver('youtube_unlisted')->getEmbedUrl($id);
 *
 * Switching providers = one line change in .env (VIDEO_DEFAULT_PROVIDER).
 * No widget or controller code needs to know which provider is active.
 */
class VideoManager
{
    public function driver(?string $name = null): VideoProviderInterface
    {
        $name ??= config('video.default_provider', 'youtube_unlisted');

        return match ($name) {
            'youtube_unlisted'  => app(YouTubeUnlistedProvider::class),
            'cloudflare_stream' => app(CloudflareStreamProvider::class),
            'local'             => app(LocalStorageProvider::class),
            'instagram_reel'    => app(InstagramReelProvider::class),
            default             => throw new \InvalidArgumentException(
                "Unknown video provider [{$name}]. Check VIDEO_DEFAULT_PROVIDER in .env."
            ),
        };
    }
}
