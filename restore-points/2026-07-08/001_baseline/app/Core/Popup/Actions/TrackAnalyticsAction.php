<?php

namespace App\Core\Popup\Actions;

use App\Core\Popup\DTOs\AnalyticsDTO;
use App\Core\Popup\Services\AnalyticsService;

class TrackAnalyticsAction
{
    public function __construct(private readonly AnalyticsService $analyticsService) {}

    public function execute(AnalyticsDTO $dto): void
    {
        $this->analyticsService->track($dto);
    }
}
