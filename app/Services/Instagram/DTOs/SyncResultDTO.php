<?php

declare(strict_types=1);

namespace App\Services\Instagram\DTOs;

final class SyncResultDTO
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $status,
        public readonly string $message,
        public readonly int $created = 0,
        public readonly int $updated = 0,
        public readonly int $failed = 0,
        public readonly float $executionTime = 0.0,
        public readonly ?array $apiResponse = null,
    ) {}
}
