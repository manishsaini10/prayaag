<?php

namespace App\Core\Popup\DTOs;

class AnalyticsDTO
{
    public function __construct(
        public readonly string $popupId,
        public readonly string $eventType,
        public readonly ?string $variationId = null,
        public readonly ?string $sessionId = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
        public readonly ?string $url = null,
        public readonly ?string $referrer = null,
        public readonly ?string $utmSource = null,
        public readonly ?string $utmMedium = null,
        public readonly ?string $utmCampaign = null,
        public readonly array $extraData = [],
    ) {}

    public function toArray(): array
    {
        return [
            'popup_id' => $this->popupId,
            'event_type' => $this->eventType,
            'variation_id' => $this->variationId,
            'session_id' => $this->sessionId,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'url' => $this->url,
            'referrer' => $this->referrer,
            'utm_source' => $this->utmSource,
            'utm_medium' => $this->utmMedium,
            'utm_campaign' => $this->utmCampaign,
            'extra_data' => $this->extraData,
            'occurred_at' => now(),
        ];
    }
}
