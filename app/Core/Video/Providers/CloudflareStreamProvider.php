<?php

namespace App\Core\Video\Providers;

use App\Core\Video\DTO\UploadedVideoResult;
use Illuminate\Http\UploadedFile;

/**
 * Cloudflare Stream Provider — Phase 5 full implementation.
 *
 * Phase 1–4 stub: all methods throw RuntimeException until
 * CLOUDFLARE_STREAM_ACCOUNT_ID + CLOUDFLARE_STREAM_API_TOKEN are configured
 * and Phase 5 is implemented.
 *
 * When Phase 5 is ready:
 *   - upload(): POST to https://api.cloudflare.com/client/v4/accounts/{id}/stream
 *     (or TUS protocol for large files via /stream?uploadType=tus)
 *   - HLS/DASH adaptive bitrate handled automatically by Cloudflare — no extra work.
 *   - Billed per 1,000 minutes stored + delivered (cheapest at school-scale volume).
 *
 * Required .env keys for Phase 5:
 *   CLOUDFLARE_STREAM_ACCOUNT_ID=...
 *   CLOUDFLARE_STREAM_API_TOKEN=...
 *   CLOUDFLARE_STREAM_CUSTOMER_CODE=...  (the customer subdomain prefix)
 */
class CloudflareStreamProvider implements VideoProviderInterface
{
    public function key(): string
    {
        return 'cloudflare_stream';
    }

    public function upload(UploadedFile|string $source, array $meta = []): UploadedVideoResult
    {
        $this->notImplemented();
    }

    public function getEmbedUrl(string $externalId): string
    {
        $customerCode = config('video.cloudflare.customer_code');
        if ($customerCode) {
            return "https://customer-{$customerCode}.cloudflarestream.com/{$externalId}/iframe";
        }
        $this->notImplemented();
    }

    public function getThumbnailUrl(string $externalId): string
    {
        $customerCode = config('video.cloudflare.customer_code');
        if ($customerCode) {
            return "https://customer-{$customerCode}.cloudflarestream.com/{$externalId}/thumbnails/thumbnail.jpg";
        }
        $this->notImplemented();
    }

    public function getDurationSeconds(string $externalId): ?int
    {
        return null;
    }

    public function delete(string $externalId): bool
    {
        $this->notImplemented();
    }

    private function notImplemented(): never
    {
        throw new \RuntimeException(
            'CloudflareStreamProvider is not yet fully implemented (Phase 5). ' .
            'Set VIDEO_DEFAULT_PROVIDER=youtube_unlisted in .env to use the free fallback.'
        );
    }
}
