<?php

namespace App\Core\Video\Providers;

use App\Core\Video\DTO\UploadedVideoResult;
use Illuminate\Http\UploadedFile;

interface VideoProviderInterface
{
    /**
     * Upload a raw video file (or register an external URL/ID) and return
     * a normalized result. Production providers MUST NOT store the raw
     * file inside the Laravel app's own storage.
     *
     * @param  UploadedFile|string  $source  Local file or external URL/ID
     * @param  array<string,mixed>  $meta    Optional metadata (title, etc.)
     */
    public function upload(UploadedFile|string $source, array $meta = []): UploadedVideoResult;

    /** Return an iframe/HTML5-embeddable URL for the given external video ID. */
    public function getEmbedUrl(string $externalId): string;

    /** Return a static thumbnail/poster image URL. */
    public function getThumbnailUrl(string $externalId): string;

    /**
     * Attempt to fetch/refresh duration in seconds.
     * Returns null if unknown yet (e.g. video still processing).
     */
    public function getDurationSeconds(string $externalId): ?int;

    /** Delete the video from the remote provider. Returns true on success. */
    public function delete(string $externalId): bool;

    /** Machine-readable key used in the video_provider DB column. */
    public function key(): string;
}
