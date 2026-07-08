<?php

namespace App\Core\Popup\DTOs;

class PopupDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $slug,
        public readonly string $type,
        public readonly string $status = 'draft',
        public readonly ?string $categoryId = null,
        public readonly ?string $templateId = null,
        public readonly array $structure = [],
        public readonly array $settings = [],
        public readonly array $design = [],
        public readonly array $styles = [],
        public readonly ?string $customCss = null,
        public readonly ?string $customJs = null,
        public readonly ?string $startsAt = null,
        public readonly ?string $endsAt = null,
        public readonly bool $useRecurringSchedule = false,
        public readonly array $recurringSchedule = [],
        public readonly string $frequencyType = 'once_per_session',
        public readonly int $frequencyDelay = 0,
        public readonly ?int $frequencyXDays = null,
        public readonly ?int $maxViewsPerUser = null,
        public readonly bool $isAbTest = false,
        public readonly ?string $abTestId = null,
        public readonly int $priority = 0,
        public readonly array $meta = [],
        public readonly bool $noindex = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            slug: $data['slug'] ?? str($data['title'])->slug(),
            type: $data['type'] ?? 'modal',
            status: $data['status'] ?? 'draft',
            categoryId: $data['category_id'] ?? null,
            templateId: $data['template_id'] ?? null,
            structure: $data['structure'] ?? [],
            settings: $data['settings'] ?? [],
            design: $data['design'] ?? [],
            styles: $data['styles'] ?? [],
            customCss: $data['custom_css'] ?? null,
            customJs: $data['custom_js'] ?? null,
            startsAt: $data['starts_at'] ?? null,
            endsAt: $data['ends_at'] ?? null,
            useRecurringSchedule: $data['use_recurring_schedule'] ?? false,
            recurringSchedule: $data['recurring_schedule'] ?? [],
            frequencyType: $data['frequency_type'] ?? 'once_per_session',
            frequencyDelay: $data['frequency_delay'] ?? 0,
            frequencyXDays: $data['frequency_x_days'] ?? null,
            maxViewsPerUser: $data['max_views_per_user'] ?? null,
            isAbTest: $data['is_ab_test'] ?? false,
            abTestId: $data['ab_test_id'] ?? null,
            priority: $data['priority'] ?? 0,
            meta: $data['meta'] ?? [],
            noindex: $data['noindex'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'status' => $this->status,
            'category_id' => $this->categoryId,
            'template_id' => $this->templateId,
            'structure' => $this->structure,
            'settings' => $this->settings,
            'design' => $this->design,
            'styles' => $this->styles,
            'custom_css' => $this->customCss,
            'custom_js' => $this->customJs,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'use_recurring_schedule' => $this->useRecurringSchedule,
            'recurring_schedule' => $this->recurringSchedule,
            'frequency_type' => $this->frequencyType,
            'frequency_delay' => $this->frequencyDelay,
            'frequency_x_days' => $this->frequencyXDays,
            'max_views_per_user' => $this->maxViewsPerUser,
            'is_ab_test' => $this->isAbTest,
            'ab_test_id' => $this->abTestId,
            'priority' => $this->priority,
            'meta' => $this->meta,
            'noindex' => $this->noindex,
        ];
    }
}
