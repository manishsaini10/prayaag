<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageOptimizerService
{
    /**
     * Generate optimized responsive images for the given storage path on a disk.
     *
     * @param string $path The path to the uploaded file on the disk (e.g. 'uploads/ulid.jpg')
     * @param string $mimeType The mime type of the file
     * @param string $disk The disk name (default 'public')
     * @return array List of generated size paths
     */
    public static function generateSizes(string $path, string $mimeType, string $disk = 'public'): array
    {
        if (!str_starts_with($mimeType, 'image/') || $mimeType === 'image/svg+xml') {
            return []; // Only optimize standard images (non-SVG)
        }

        $realPath = Storage::disk($disk)->path($path);
        if (!file_exists($realPath)) {
            return [];
        }

        $info = @getimagesize($realPath);
        if (!$info) {
            return [];
        }

        $width = $info[0];
        $height = $info[1];

        // Load source image
        $srcImage = self::createImageFromPath($realPath, $mimeType);
        if (!$srcImage) {
            return [];
        }

        $pathInfo = pathinfo($path);
        $dir = $pathInfo['dirname'] ?? '';
        $dirPrefix = $dir ? $dir . '/' : '';
        $filename = $pathInfo['filename'];
        $ext = $pathInfo['extension'] ?? 'jpg';

        $generated = [];

        // 1. Thumbnail (150x150 center crop)
        $thumbPath = $dirPrefix . $filename . '-thumb.' . $ext;
        $thumbReal = Storage::disk($disk)->path($thumbPath);
        $thumbImg = self::cropCenter($srcImage, $width, $height, 150);
        if ($thumbImg) {
            if (self::saveImageToDisk($thumbImg, $thumbReal, $mimeType)) {
                $generated['thumb'] = $thumbPath;
            }
            imagedestroy($thumbImg);
        }

        // 2. Medium (proportional, max 600px)
        $mediumPath = $dirPrefix . $filename . '-medium.' . $ext;
        $mediumReal = Storage::disk($disk)->path($mediumPath);
        $mediumImg = self::resizeProportional($srcImage, $width, $height, 600);
        if ($mediumImg) {
            if (self::saveImageToDisk($mediumImg, $mediumReal, $mimeType)) {
                $generated['medium'] = $mediumPath;
            }
            imagedestroy($mediumImg);
        }

        // 3. Large (proportional, max 1200px)
        $largePath = $dirPrefix . $filename . '-large.' . $ext;
        $largeReal = Storage::disk($disk)->path($largePath);
        $largeImg = self::resizeProportional($srcImage, $width, $height, 1200);
        if ($largeImg) {
            if (self::saveImageToDisk($largeImg, $largeReal, $mimeType)) {
                $generated['large'] = $largePath;
            }
            imagedestroy($largeImg);
        }

        imagedestroy($srcImage);

        return $generated;
    }

    /**
     * Crop from center of the image to produce a square thumbnail.
     */
    private static function cropCenter($srcImage, int $width, int $height, int $targetSize = 150)
    {
        $target = imagecreatetruecolor($targetSize, $targetSize);
        if (!$target) return null;

        // Maintain transparency
        imagealphablending($target, false);
        imagesavealpha($target, true);

        if ($width > $height) {
            // Landscape
            $srcHeight = $height;
            $srcWidth = $height;
            $srcX = (int) (($width - $height) / 2);
            $srcY = 0;
        } else {
            // Portrait
            $srcWidth = $width;
            $srcHeight = $width;
            $srcX = 0;
            $srcY = (int) (($height - $width) / 2);
        }

        imagecopyresampled($target, $srcImage, 0, 0, $srcX, $srcY, $targetSize, $targetSize, $srcWidth, $srcHeight);
        return $target;
    }

    /**
     * Resize proportionally so the largest dimension is at most $maxDimension.
     */
    private static function resizeProportional($srcImage, int $width, int $height, int $maxDimension)
    {
        if ($width <= $maxDimension && $height <= $maxDimension) {
            $newWidth = $width;
            $newHeight = $height;
        } else {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($height / $width) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($width / $height) * $maxDimension);
            }
        }

        $target = imagecreatetruecolor($newWidth, $newHeight);
        if (!$target) return null;

        // Maintain transparency
        imagealphablending($target, false);
        imagesavealpha($target, true);

        imagecopyresampled($target, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        return $target;
    }

    /**
     * Load source image based on mime type.
     */
    private static function createImageFromPath(string $path, string $mimeType)
    {
        try {
            return match ($mimeType) {
                'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
                'image/png' => @imagecreatefrompng($path),
                'image/webp' => @imagecreatefromwebp($path),
                'image/gif' => @imagecreatefromgif($path),
                default => null,
            };
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Save generated GD resource to disk.
     */
    private static function saveImageToDisk($image, string $outputPath, string $mimeType, int $quality = 80): bool
    {
        try {
            return match ($mimeType) {
                'image/jpeg', 'image/jpg' => @imagejpeg($image, $outputPath, $quality),
                'image/png' => @imagepng($image, $outputPath, 6), // 0-9 compression scale
                'image/webp' => @imagewebp($image, $outputPath, $quality),
                'image/gif' => @imagegif($image, $outputPath),
                default => false,
            };
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Delete all resized versions of a file.
     */
    public static function deleteSizes(string $path, string $disk = 'public'): void
    {
        $pathInfo = pathinfo($path);
        $dir = $pathInfo['dirname'] ?? '';
        $dirPrefix = $dir ? $dir . '/' : '';
        $filename = $pathInfo['filename'];
        $ext = $pathInfo['extension'] ?? '';

        if (!$ext) return;

        $sizes = ['thumb', 'medium', 'large'];
        foreach ($sizes as $size) {
            $sizePath = $dirPrefix . $filename . '-' . $size . '.' . $ext;
            if (Storage::disk($disk)->exists($sizePath)) {
                Storage::disk($disk)->delete($sizePath);
            }
        }
    }
}
