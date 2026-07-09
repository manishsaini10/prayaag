<?php

namespace App\Core\Popup\DTOs;

class LeadDTO
{
    public function __construct(
        public readonly string $popupId,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly array $formData = [],
        public readonly ?string $source = null,
        public readonly ?string $utmSource = null,
        public readonly ?string $utmMedium = null,
        public readonly ?string $utmCampaign = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
    ) {}

    public function toArray(): array
    {
        return [
            'popup_id' => $this->popupId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'form_data' => $this->formData,
            'source' => $this->source,
            'utm_source' => $this->utmSource,
            'utm_medium' => $this->utmMedium,
            'utm_campaign' => $this->utmCampaign,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'status' => 'new',
        ];
    }
}
