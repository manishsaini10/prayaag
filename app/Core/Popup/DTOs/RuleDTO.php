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
