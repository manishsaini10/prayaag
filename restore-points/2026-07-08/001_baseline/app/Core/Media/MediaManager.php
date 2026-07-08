<?php

namespace App\Core\Media;

use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Media Library engine: stores uploads on a disk, records metadata
 * (dimensions for images), and handles deletion. WebP conversion is
 * optional and only runs when GD is available.
 */
class MediaManager
{
    protected function disk(): string
    {
        return 'public';
    }

    public function store(UploadedFile $file, ?MediaFolder $folder = null, ?string $disk = null): Media
    {
        $disk = $disk ?? $this->disk();
        $directory = trim('media/' . ($folder?->path ?? ''), '/');
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension();
        $filename = (string) Str::ulid() . ($extension ? '.' . $extension : '');

        $path = $file->storeAs($directory, $filename, $disk);

        [$width, $height] = $this->dimensions($file);

        return Media::create([
            'folder_id'     => $folder?->id,
            'disk'          => $disk,
            'path'          => $path,
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'extension'     => $extension,
            'size'          => $file->getSize(),
            'width'         => $width,
            'height'        => $height,
        ]);
    }

    public function url(Media $media): string
    {
        return Storage::disk($media->disk)->url($media->path);
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }

    /**
     * Convert an image media item to WebP using native GD, if available.
     * Returns the new Media record, or null when GD/source is unsupported.
     */
    public function toWebp(Media $media, int $quality = 80): ?Media
    {
        if (! function_exists('imagewebp') || ! str_starts_with((string) $media->mime_type, 'image/')) {
            return null;
        }

        $disk = Storage::disk($media->disk);
        $source = @imagecreatefromstring($disk->get($media->path));

        if ($source === false) {
            return null;
        }

        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $media->path);

        ob_start();
        imagewebp($source, null, $quality);
        $binary = ob_get_clean();
        imagedestroy($source);

        $disk->put($webpPath, $binary);

        return Media::create([
            'folder_id'     => $media->folder_id,
            'disk'          => $media->disk,
            'path'          => $webpPath,
            'filename'      => basename($webpPath),
            'original_name' => pathinfo($media->original_name, PATHINFO_FILENAME) . '.webp',
            'mime_type'     => 'image/webp',
            'extension'     => 'webp',
            'size'          => strlen($binary),
            'width'         => $media->width,
            'height'        => $media->height,
        ]);
    }

    /** @return array{0: int|null, 1: int|null} */
    protected function dimensions(UploadedFile $file): array
    {
        if (str_starts_with((string) $file->getMimeType(), 'image/')) {
            $info = @getimagesize($file->getPathname());

            if ($info) {
                return [$info[0], $info[1]];
            }
        }

        return [null, null];
    }
}
