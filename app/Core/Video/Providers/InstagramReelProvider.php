<?php

namespace App\Core\Video\Providers;

use App\Core\Video\DTO\UploadedVideoResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Instagram Reel Provider — link URL import or Graph API sync.
 *
 * Supported URL Formats:
 *   https://www.instagram.com/reel/C1234567890/
 *   https://www.instagram.com/p/C1234567890/
 *   https://instagr.am/p/C1234567890/
 *   Bare shortcode: C1234567890
 */
class InstagramReelProvider implements VideoProviderInterface
{
    public function key(): string
    {
        return 'instagram_reel';
    }

    public function upload(UploadedFile|string $source, array $meta = []): UploadedVideoResult
    {
        if ($source instanceof UploadedFile) {
            throw new \InvalidArgumentException('Direct file upload to Instagram is not supported. Provide an Instagram Reel link or shortcode.');
        }

        $shortcode = $this->extractShortcode($source);

        if (! $shortcode) {
            throw new \InvalidArgumentException("Could not extract Instagram shortcode from: {$source}");
        }

        return new UploadedVideoResult(
            id:              $shortcode,
            embedUrl:        $this->getEmbedUrl($shortcode),
            thumbnailUrl:    $this->getThumbnailUrl($shortcode),
            durationSeconds: null,
            status:          'ready',
            provider:        $this->key(),
        );
    }

    public function getEmbedUrl(string $externalId): string
    {
        return "https://www.instagram.com/p/{$externalId}/embed/";
    }

    public function getThumbnailUrl(string $externalId): string
    {
        // Try fetching oEmbed thumbnail if available
        try {
            $response = Http::timeout(3)->get("https://api.instagram.com/oembed", [
                'url' => "https://www.instagram.com/p/{$externalId}/",
            ]);

            if ($response->successful() && isset($response->json()['thumbnail_url'])) {
                return $response->json()['thumbnail_url'];
            }
        } catch (\Throwable $e) {
            Log::debug('Instagram oEmbed thumbnail fetch failed', ['id' => $externalId, 'error' => $e->getMessage()]);
        }

        // Fallback thumbnail URL via Instagram media redirect or placeholder
        return "https://www.instagram.com/p/{$externalId}/media/?size=l";
    }

    public function getDurationSeconds(string $externalId): ?int
    {
        return null;
    }

    public function delete(string $externalId): bool
    {
        return true;
    }

    // ----------------------------------------------------------------
    // Public Helper
    // ----------------------------------------------------------------

    public function extractShortcode(string $input): ?string
    {
        $input = trim($input);

        // Plain shortcode (usually 9-14 alphanumeric chars / _ / -)
        if (preg_match('/^[A-Za-z0-9_\-]{9,15}$/', $input) && ! str_contains($input, 'http')) {
            return $input;
        }

        // instagram.com/reel/SHORTCODE/  or instagram.com/p/SHORTCODE/
        if (preg_match('~instagram\.com/(?:reel|p)/([A-Za-z0-9_\-]{9,15})~i', $input, $m)) {
            return $m[1];
        }

        // instagr.am/p/SHORTCODE/
        if (preg_match('~instagr\.am/(?:reel|p)/([A-Za-z0-9_\-]{9,15})~i', $input, $m)) {
            return $m[1];
        }

        return null;
    }
}
