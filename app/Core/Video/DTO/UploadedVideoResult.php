<?php

namespace App\Core\Video\DTO;

readonly class UploadedVideoResult
{
    public function __construct(
        public string  $id,
        public string  $embedUrl,
        public string  $thumbnailUrl,
        public ?int    $durationSeconds,
        public string  $status = 'ready',  // 'ready' | 'processing' | 'error'
        public ?string $provider = null,
    ) {}
}
