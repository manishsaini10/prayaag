<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Tours & Excursions Page Widget — Educational Trips, Cultural Exchange & Adventure Programs
 */
class ToursExcursionsPageWidget extends AbstractWidget
{
    public function type(): string { return 'tours-excursions-page'; }
    public function label(): string { return 'Tours & Excursions (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Tours & Educational Excursions',
            'hero_subtitle' => 'At Prayaag, we believe that tours and excursions are the perfect way to expand one\'s horizon. Students are encouraged to acquire knowledge and explore the world beyond classroom boundaries through curated educational journeys.',
            'hero_bg'       => '/storage/media/imported/01KWEBD5DV5S888ZVB19SP12ZD.jpg',

            'programs' => [
                [
                    'icon'  => '🏛️',
                    'title' => 'Educational Historical Tours',
                    'desc'  => 'Structured visits to historical monuments, museums, science centres, and heritage sites across India that bring textbook knowledge to life and build historical consciousness.',
                ],
                [
                    'icon'  => '🌍',
                    'title' => 'International Exchange Programs',
                    'desc'  => 'Regular International Educational Exchange Programs offer students global exposure, cross-cultural understanding, and opportunities to collaborate with peers from around the world.',
                ],
                [
                    'icon'  => '🏕️',
                    'title' => 'Adventure & Trekking Camps',
                    'desc'  => 'Mountain treks, nature hikes, and outdoor adventure camps build resilience, teamwork, leadership skills, and a deep connection with the natural environment.',
                ],
                [
                    'icon'  => '🎭',
                    'title' => 'Cultural & Arts Immersions',
                    'desc'  => 'Visits to theatres, art galleries, folk festivals, and cultural performances enrich students\' understanding of art, music, literature, and India\'s diverse heritage.',
                ],
                [
                    'icon'  => '🔬',
                    'title' => 'Science & Technology Visits',
                    'desc'  => 'Industry visits to research laboratories, technology parks, space centres, and manufacturing plants give students real-world industry exposure and career inspiration.',
                ],
                [
                    'icon'  => '🤝',
                    'title' => 'Community Service Trips',
                    'desc'  => 'Social outreach excursions to NGOs, rural communities, and charitable organisations build empathy, social responsibility, and civic awareness in students.',
                ],
            ],

            'past_destinations' => [
                '🗼 Eiffel Tower & Euro Cultural Tour (Senior Wing)',
                '🇸🇬 Singapore Science & Technology Exchange',
                '🏔️ Himalayan Adventure Trek — Manali & Rohtang',
                '🏛️ Delhi Heritage Circuit — Red Fort, Qutub, Parliament',
                '🔭 National Science Centre, New Delhi',
                '🌊 Goa — Marine Biology & Coastal Ecology Program',
            ],

            'cta_url'   => '/contact',
            'cta_label' => 'Enquire About Upcoming Excursions',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'cta_label',     'label' => 'CTA Label',     'type' => 'text'],
            ['key' => 'cta_url',       'label' => 'CTA URL',       'type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.tours-excursions-page', $settings)->render();
    }
}
