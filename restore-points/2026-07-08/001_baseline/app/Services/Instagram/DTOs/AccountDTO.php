<?php

declare(strict_types=1);

namespace App\Services\Instagram\DTOs;

final class AccountDTO
{
    public function __construct(
        public readonly string $facebookPageId,
        public readonly string $instagramBusinessId,
        public readonly string $username,
        public readonly string $name,
        public readonly ?string $profilePicture,
        public readonly int $followers,
        public readonly int $mediaCount,
        public readonly string $token,
        public readonly ?string $refreshToken,
        public readonly \DateTimeInterface $expiresAt,
    ) {}

    public static function fromGraphApi(array $data, string $token, \DateTimeInterface $expiresAt): self
    {
        return new self(
            facebookPageId: $data['id'] ?? '',
            instagramBusinessId: $data['instagram_business_account']['id'] ?? '',
            username: $data['username'] ?? '',
            name: $data['name'] ?? '',
            profilePicture: $data['profile_picture_url'] ?? null,
            followers: (int) ($data['followers_count'] ?? 0),
            mediaCount: (int) ($data['media_count'] ?? 0),
            token: $token,
            refreshToken: null,
            expiresAt: $expiresAt,
        );
    }
}
