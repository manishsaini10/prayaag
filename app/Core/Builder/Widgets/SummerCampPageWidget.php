<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Summer Camp Page Widget — Activity Modules, Schedule, Age Groups & Registration
 */
class SummerCampPageWidget extends AbstractWidget
{
    public function type(): string { return 'summer-camp-page'; }
    public function label(): string { return 'Summer Camp (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'     => 'Summer Camp Adventure Awaits!',
            'hero_subtitle'  => 'Enriching, Exciting, and Educational Camp Experiences for Young Explorers! Join Prayaag International School\'s annual Summer Camp — where learning meets fun.',
            'hero_bg'        => '/storage/media/imported/01KTQX13TDVNX7JCAX9PTCMMQT.webp',

            'year'           => '2025',
            'dates'          => '22nd May – 31st May 2025',
            'timings'        => '7:45 AM – 10:30 AM',
            'charges'        => '₹1,500 per student',
            'contact_url'    => '/contact-us',

            'about' => 'Prayaag International School\'s Summer Camp is a unique blend of fun, creativity, sports, and STEM learning designed to engage students during their summer break. Each activity is carefully curated by expert educators and coaches to ensure children learn, grow, and thrive.',

            'highlights' => [
                ['icon' => '🏕️', 'value' => '10 Days', 'label' => 'Action-Packed Camp'],
                ['icon' => '🎯', 'value' => '15+',      'label' => 'Activity Modules'],
                ['icon' => '👩‍🏫', 'value' => 'Expert',  'label' => 'Coaches & Mentors'],
                ['icon' => '🎓', 'value' => 'All Ages', 'label' => 'Pre-Nursery to VIII'],
            ],

            'activity_categories' => [
                [
                    'icon'       => '🌿',
                    'name'       => 'Nature Exploration',
                    'desc'       => 'Bird watching, gardening, environmental science projects, and outdoor nature walks to build environmental awareness and curiosity.',
                    'age_group'  => 'All Age Groups',
                ],
                [
                    'icon'       => '🎨',
                    'name'       => 'Arts & Crafts',
                    'desc'       => 'Painting, clay modelling, origami, fabric art, and mixed media projects that unleash creativity and fine motor skills.',
                    'age_group'  => 'Pre-Nursery – V',
                ],
                [
                    'icon'       => '⚽',
                    'name'       => 'Sports & Games',
                    'desc'       => 'Cricket, football, badminton, swimming, yoga, and fun group games coached by trained sports professionals.',
                    'age_group'  => 'Classes I – VIII',
                ],
                [
                    'icon'       => '🤖',
                    'name'       => 'STEM Exploration',
                    'desc'       => 'Robotics building, science experiments, coding challenges, and maker-space projects that ignite innovation and problem-solving.',
                    'age_group'  => 'Classes III – VIII',
                ],
                [
                    'icon'       => '🎭',
                    'name'       => 'Performing Arts',
                    'desc'       => 'Drama workshops, dance routines, puppet shows, and storytelling sessions that build confidence and self-expression.',
                    'age_group'  => 'Pre-Nursery – VI',
                ],
                [
                    'icon'       => '🧘',
                    'name'       => 'Wellness & Mindfulness',
                    'desc'       => 'Morning yoga, breathing exercises, mindfulness activities, and wellness games to build healthy habits from an early age.',
                    'age_group'  => 'All Age Groups',
                ],
            ],

            'schedule' => [
                [
                    'group'    => 'Pre-Nursery – Class II',
                    'slot'     => '7:45 AM – 9:00 AM',
                    'activity1' => 'Arts & Crafts / Dance & Music',
                    'activity2' => 'Nature Exploration / Wellness',
                    'capacity' => '30 students per batch',
                ],
                [
                    'group'    => 'Class III – Class VIII',
                    'slot'     => '9:00 AM – 10:30 AM',
                    'activity1' => 'Sports & Games / STEM',
                    'activity2' => 'Performing Arts / Wellness',
                    'capacity' => '35 students per batch',
                ],
            ],

            'note' => 'Limited seats available. Early registration is recommended. All activities are supervised by trained staff. Participants will receive a participation certificate and a camp kit on the first day.',

            'cta_label' => 'Register for Summer Camp',
            'cta_url'   => '/contact-us',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',     'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle',  'type' => 'textarea'],
            ['key' => 'year',          'label' => 'Camp Year',      'type' => 'text'],
            ['key' => 'dates',         'label' => 'Camp Dates',     'type' => 'text'],
            ['key' => 'timings',       'label' => 'Timings',        'type' => 'text'],
            ['key' => 'charges',       'label' => 'Charges',        'type' => 'text'],
            ['key' => 'about',         'label' => 'About Section',  'type' => 'textarea'],
            ['key' => 'note',          'label' => 'Important Note', 'type' => 'textarea'],
            ['key' => 'cta_label',     'label' => 'CTA Label',      'type' => 'text'],
            ['key' => 'cta_url',       'label' => 'CTA URL',        'type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.summer-camp-page', $settings)->render();
    }
}
