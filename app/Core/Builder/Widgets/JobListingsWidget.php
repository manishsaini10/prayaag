<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Models\JobListing;

/**
 * Dynamic widget: lists currently open job listings.
 */
class JobListingsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'job_listings';
    }

    public function label(): string
    {
        return 'Job Listings';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return ['limit' => 10];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $limit = max(1, (int) $this->setting($settings, 'limit', 10));

        $jobs = JobListing::open()->latest()->limit($limit)->get();

        if ($jobs->isEmpty()) {
            return '<div class="pb-jobs pb-empty"></div>';
        }

        $items = '';
        foreach ($jobs as $job) {
            $meta = array_filter([$job->department, $job->location]);
            $sub = $meta ? ' <span class="pb-job__meta">' . $this->e(implode(' · ', $meta)) . '</span>' : '';
            $items .= '<li class="pb-job">' . $this->e($job->title) . $sub . '</li>';
        }

        return '<div class="pb-jobs"><ul>' . $items . '</ul></div>';
    }
}
