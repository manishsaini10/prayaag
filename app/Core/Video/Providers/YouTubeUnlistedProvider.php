<?php

namespace App\Core\Video\Providers;

use App\Core\Video\DTO\UploadedVideoResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * YouTube Unlisted Provider — free default.
 *
 * Phase 1: URL/ID import only (no raw file upload to YouTube API yet).
 * Phase 3: Raw file upload via queued ProcessVideoUploadJob using google/apiclient.
 *
 * Config keys (optional for Phase 1 URL-import):
 *   YOUTUBE_CLIENT_ID, YOUTUBE_CLIENT_SECRET, YOUTUBE_CHANNEL_REFRESH_TOKEN
 */
class YouTubeUnlistedProvider implements VideoProviderInterface
{
    public function key(): string
    {
        return 'youtube_unlisted';
    }

    /**
     * Phase 1 implementation: $source is a YouTube URL or video ID string.
     * Extracts video ID and returns normalized result without hitting any API.
     *
     * Examples of accepted $source values:
     *   "dQw4w9WgXcQ"
     *   "https://youtu.be/dQw4w9WgXcQ"
     *   "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
     */
    public function upload(UploadedFile|string $source, array $meta = []): UploadedVideoResult
    {
        if ($source instanceof UploadedFile) {
            // Phase 3 will implement actual file upload via queued job.
            throw new \RuntimeException(
                'Direct file upload to YouTube is handled by ProcessVideoUploadJob (Phase 3). ' .
                'Pass the YouTube video URL or ID as a string for Phase 1 import.'
            );
        }

        $videoId = $this->extractVideoId($source);

        if (! $videoId) {
            throw new \InvalidArgumentException("Could not extract a YouTube video ID from: {$source}");
        }

        return new UploadedVideoResult(
            id:              $videoId,
            embedUrl:        $this->getEmbedUrl($videoId),
            thumbnailUrl:    $this->getThumbnailUrl($videoId),
            durationSeconds: null,   // async — will be fetched later
            status:          'ready',
            provider:        $this->key(),
        );
    }

    public function getEmbedUrl(string $externalId): string
    {
        return "https://www.youtube-nocookie.com/embed/{$externalId}?rel=0&modestbranding=1";
    }

    public function getThumbnailUrl(string $externalId): string
    {
        return "https://img.youtube.com/vi/{$externalId}/hqdefault.jpg";
    }

    /**
     * Phase 1 stub — returns null.
     * Phase 3 will call videos.list with part=contentDetails and parse ISO 8601.
     */
    public function getDurationSeconds(string $externalId): ?int
    {
        return null;
    }

    /**
     * Phase 1 stub — returns true.
     * Phase 3 will call videos.delete via Google API client.
     */
    public function delete(string $externalId): bool
    {
        return true;
    }

    // ----------------------------------------------------------------
    // Public helpers
    // ----------------------------------------------------------------

    public function extractVideoId(string $input): ?string
    {
        $input = trim($input);

        // Plain video ID (11 chars, alphanumeric + _ -)
        if (preg_match('/^[A-Za-z0-9_\-]{11}$/', $input)) {
            return $input;
        }

        // youtube.com/shorts/VIDEO_ID
        if (preg_match('~shorts/([A-Za-z0-9_\-]{11})~', $input, $m)) {
            return $m[1];
        }

        // youtu.be/VIDEO_ID
        if (preg_match('~youtu\.be/([A-Za-z0-9_\-]{11})~', $input, $m)) {
            return $m[1];
        }

        // youtube.com/watch?v=VIDEO_ID
        if (preg_match('~[?&]v=([A-Za-z0-9_\-]{11})~', $input, $m)) {
            return $m[1];
        }

        // youtube.com/embed/VIDEO_ID
        if (preg_match('~embed/([A-Za-z0-9_\-]{11})~', $input, $m)) {
            return $m[1];
        }

        return null;
    }
}
