<?php

declare(strict_types=1);

namespace App\Services\Instagram\DTOs;

final class MediaDTO
{
    public function __construct(
        public readonly string $mediaId,
        public readonly string $mediaType,
        public readonly string $mediaUrl,
        public readonly ?string $thumbnailUrl,
        public readonly ?string $permalink,
        public readonly ?string $caption,
        public readonly ?\DateTimeInterface $timestamp,
        public readonly int $likeCount,
        public readonly int $commentCount,
        public readonly array $children = [],
    ) {}

    public static function fromGraphApi(array $item): self
    {
        return new self(
            mediaId: $item['id'] ?? '',
            mediaType: $item['media_type'] ?? 'IMAGE',
            mediaUrl: $item['media_url'] ?? '',
            thumbnailUrl: $item['thumbnail_url'] ?? ($item['media_url'] ?? null),
            permalink: $item['permalink'] ?? null,
            caption: $item['caption'] ?? null,
            timestamp: isset($item['timestamp']) ? new \DateTimeImmutable($item['timestamp']) : null,
            likeCount: (int) ($item['like_count'] ?? 0),
            commentCount: (int) ($item['comments_count'] ?? 0),
            children: $item['children']['data'] ?? [],
        );
    }
}
