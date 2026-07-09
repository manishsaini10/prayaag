<?php

namespace App\Core\Popup\DTOs;

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
