<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Instagram\InstagramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SyncInstagramProfile implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        private readonly string $accountId,
    ) {}

    public function handle(InstagramService $service): void
    {
        $service->sync($this->accountId);
    }
}
