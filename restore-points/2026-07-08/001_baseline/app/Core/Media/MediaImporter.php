<?php

namespace App\Core\Media;

use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Imports images from external URLs into the local media library.
 * Downloads the file, stores on the public disk, records a Media row
 * with metadata, and returns a mapping from the original URL to the
 * new local storage path.
 */
class MediaImporter
{
    protected array $imported = [];

    public function imported(): array
    {
        return $this->imported;
    }

    /**
     * Import an image from a URL. Returns the local storage URL on success,
     * or null on failure. Idempotent: re-importing the same URL returns
     * the previously stored path.
     */
    public function import(string $url, ?MediaFolder $folder = null, string $disk = 'public'): ?string
    {
        if (isset($this->imported[$url])) {
            return $this->imported[$url];
        }

        $contents = @file_get_contents($url, false, stream_context_create([
            'http' => ['timeout' => 15, 'user_agent' => 'PrayaagCMS/1.0'],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
        ]));

        if ($contents === false) {
            return null;
        }

        $ext = $this->guessExtension($url, $contents);
        $filename = (string) Str::ulid() . ($ext ? '.' . $ext : '');
        $directory = 'media/imported';

        Storage::disk($disk)->put($directory . '/' . $filename, $contents);

        $path = $directory . '/' . $filename;

        [$width, $height] = $this->dimensions($contents);

        $originalName = basename(parse_url($url, PHP_URL_PATH) ?: $filename);

        Media::create([
            'folder_id'     => $folder?->id,
            'disk'          => $disk,
            'path'          => $path,
            'filename'      => $filename,
            'original_name' => $originalName,
            'mime_type'     => $this->mimeType($ext),
            'extension'     => $ext ?: null,
            'size'          => strlen($contents),
            'width'         => $width,
            'height'        => $height,
        ]);

        $this->imported[$url] = $path;

        return $path;
    }

    /** Guess extension from URL path or content signature. */
    protected function guessExtension(string $url, string $contents): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'avif'], true)) {
            return $ext;
        }

        // Fall back to sniffing the first bytes.
        if (str_starts_with($contents, "\x89\x50\x4E\x47")) return 'png';
        if (str_starts_with($contents, "\xFF\xD8")) return 'jpg';
        if (str_starts_with($contents, "GIF87a") || str_starts_with($contents, "GIF89a")) return 'gif';
        if (str_starts_with($contents, "RIFF") && str_contains(substr($contents, 0, 12), "WEBP")) return 'webp';
        if (str_starts_with($contents, "<svg") || str_starts_with($contents, "<?xml")) return 'svg';

        return 'jpg';
    }

    protected function mimeType(string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            'svg'         => 'image/svg+xml',
            'ico'         => 'image/x-icon',
            'avif'        => 'image/avif',
            default       => 'application/octet-stream',
        };
    }

    /** @return array{0: int|null, 1: int|null} */
    protected function dimensions(string $contents): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'mi_');
        file_put_contents($tmp, $contents);
        $info = @getimagesize($tmp);
        @unlink($tmp);

        if ($info) {
            return [$info[0], $info[1]];
        }

        return [null, null];
    }

    /** Build a storage URL for a given import path. */
    public static function storageUrl(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
}
