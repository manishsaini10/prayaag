<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use Illuminate\Support\Facades\Blade;

class TestimonialPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'testimonial_page';
    }

    public function label(): string
    {
        return 'Testimonial Page';
    }

    public function category(): string
    {
        return 'forms';
    }

    public function defaultSettings(): array
    {
        return [
            'hero_accent_text'  => 'Share Your Voice',
            'hero_heading'      => 'Your Story <br class="hidden lg:inline"/> <span class="bg-gradient-to-r from-purple-700 via-indigo-800 to-blue-900 text-transparent bg-clip-text">Inspires Others.</span>',
            'hero_description'  => 'At Prayaag International School, we cherish the partnership between parents and educators. Your feedback guides future families in choosing the right path for their children.',
            'show_rating_cards' => true,
            'rating_value'      => '4.9 / 5',
            'rating_label'      => 'Parent Rating',
            'verified_value'    => '100%',
            'verified_label'    => 'Verified Reviews',
            'show_guide'        => true,
            'guide_title'       => 'Review Submission Guide',
            'guide_step_1'      => 'Provide parent contact and child class info',
            'guide_step_2'      => 'Write an honest review (min 50 characters)',
            'guide_step_3'      => 'Attach an optional profile photo for cropping',
            'background_style'  => 'default',
            'form_title'        => 'Share Your Experience',
            'form_description'  => 'Let us know your feedback! Your values and stories help other parents discover the vibrant Prayaag International School community.',
            'form_button_text'  => 'Submit Experience',
            'consent_text'      => 'I hereby declare that this testimonial is based on my first-hand experience, and I authorize the school administration to review, moderate, and publish it on the public school website.',
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'background_style' => ['default', 'minimal', 'vibrant'],
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        return Blade::render('<x-testimonial-page :settings="$settings"/>', [
            'settings' => $settings,
        ]);
    }
}
