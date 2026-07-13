<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use Illuminate\Support\Facades\Blade;

/**
 * Renders the parent testimonial submission form.
 */
class TestimonialFormWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'testimonial_form';
    }

    public function label(): string
    {
        return 'Testimonial Form';
    }

    public function category(): string
    {
        return 'forms';
    }

    public function defaultSettings(): array
    {
        return [];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        return Blade::render('<x-testimonial-form />');
    }
}
