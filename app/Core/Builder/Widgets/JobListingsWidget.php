<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Models\JobListing;

/**
 * Dynamic widget: Ultra-Premium Careers Page & Job Application Portal.
 */
class JobListingsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'job_listings';
    }

    public function label(): string
    {
        return 'Job Listings & Application Portal';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return [
            'limit'   => 12,
            'heading' => 'Careers at Prayaag International',
            'eyebrow' => 'Shape The Future With Us',
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $limit = max(1, (int) $this->setting($settings, 'limit', 12));

        $jobs = JobListing::open()->latest()->limit($limit)->get();

        $categories = JobListing::open()
            ->distinct()
            ->pluck('department')
            ->filter()
            ->values();

        return view('widgets.careers-page', [
            'jobs'       => $jobs,
            'categories' => $categories,
            'settings'   => $settings,
        ])->render();
    }
}
