<?php

namespace App\Core\Video\Providers;

use App\Core\Video\DTO\UploadedVideoResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Local Storage Provider — DEV / TESTING ONLY.
 *
 * ⚠ WILL throw a RuntimeException in production environments.
 * No CDN, no adaptive bitrate, no HLS — explicitly unsuitable for real traffic.
 */
class LocalStorageProvider implements VideoProviderInterface
{
    public function key(): string
    {
        return 'local';
    }

    public function upload(UploadedFile|string $source, array $meta = []): UploadedVideoResult
    {
        $this->guardProduction();

        if (is_string($source)) {
            // Treat as already-stored filename/path
            $videoId = basename($source);
            return new UploadedVideoResult(
                id:              $videoId,
                embedUrl:        $this->getEmbedUrl($videoId),
                thumbnailUrl:    $this->getThumbnailUrl($videoId),
                durationSeconds: null,
                status:          'ready',
                provider:        $this->key(),
            );
        }

        // Store locally
        $filename = Str::uuid() . '.' . $source->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('dev-videos', $source, $filename);

        return new UploadedVideoResult(
            id:              $filename,
            embedUrl:        $this->getEmbedUrl($filename),
            thumbnailUrl:    $this->getThumbnailUrl($filename),
            durationSeconds: null,
            status:          'ready',
            provider:        $this->key(),
        );
    }

    public function getEmbedUrl(string $externalId): string
    {
        $this->guardProduction();
        return asset('storage/dev-videos/' . $externalId);
    }

    public function getThumbnailUrl(string $externalId): string
    {
        return asset('storage/dev-videos/poster.png'); // generic placeholder
    }

    public function getDurationSeconds(string $externalId): ?int
    {
        return null;
    }

    public function delete(string $externalId): bool
    {
        $this->guardProduction();
        Storage::disk('public')->delete('dev-videos/' . $externalId);
        return true;
    }

    private function guardProduction(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException(
                'LocalStorageProvider must NEVER be used in production. ' .
                'Set VIDEO_DEFAULT_PROVIDER=youtube_unlisted or cloudflare_stream in .env.'
            );
        }
    }
}
