<?php

namespace App\Core\Popup\DTOs;

class RuleDTO
{
    public function __construct(
        public readonly string $type,
        public readonly string $ruleKey,
        public readonly string $condition = 'is',
        public readonly mixed $value = null,
        public readonly array $extra = [],
        public readonly int $sortOrder = 0,
        public readonly bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            ruleKey: $data['rule_key'],
            condition: $data['condition'] ?? 'is',
            value: $data['value'] ?? null,
            extra: $data['extra'] ?? [],
            sortOrder: $data['sort_order'] ?? 0,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'rule_key' => $this->ruleKey,
            'condition' => $this->condition,
            'value' => $this->value,
            'extra' => $this->extra,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];
    }
}

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

class TemplateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $type,
        public readonly array $structure,
        public readonly array $settings = [],
        public readonly array $styles = [],
        public readonly ?string $description = null,
        public readonly ?string $category = null,
        public readonly ?string $thumbnail = null,
        public readonly bool $isPremium = false,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'structure' => $this->structure,
            'settings' => $this->settings,
            'styles' => $this->styles,
            'description' => $this->description,
            'category' => $this->category,
            'thumbnail' => $this->thumbnail,
            'is_premium' => $this->isPremium,
            'is_built_in' => true,
            'is_active' => true,
        ];
    }
}
