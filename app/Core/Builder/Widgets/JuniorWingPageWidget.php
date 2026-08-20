<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Junior Wing Page Widget — Montessori, Primary & Middle School Showcase
 */
class JuniorWingPageWidget extends AbstractWidget
{
    public function type(): string { return 'junior-wing-page'; }
    public function label(): string { return 'Junior Wing School (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Junior Wing School — Nurturing Young Minds',
            'hero_subtitle' => 'We at Prayaag believe that the initial days at school bring the best out of young learners. Our Junior Wing provides a warm, stimulating, and secure environment for students from Pre-Nursery through Class V.',
            'hero_bg'       => '/storage/media/imported/01KTQWW6KKXCCRMFVKNRDED21M.webp',
            'hero_eyebrow'  => 'Pre-Nursery to Class V',

            'intro' => 'To sow and nurture the seeds of wisdom in the formative years of education, the Junior Wing of Prayaag International School, Panipat is staffed by highly-educated and experienced teachers who inculcate the foundation of contemporary education. We strive to provide an environment that helps in building a child\'s body, mind and soul.',

            'highlights' => [
                ['icon' => '🏫', 'stat' => 'Self-Contained', 'label' => 'Dedicated Building'],
                ['icon' => '📱', 'stat' => 'Smart',          'label' => 'Classrooms'],
                ['icon' => '👁️', 'stat' => '360°',           'label' => 'Surveillance'],
                ['icon' => '👨‍🏫', 'stat' => '1:20',          'label' => 'Teacher Ratio'],
            ],

            'features' => [
                [
                    'icon'  => '📱',
                    'title' => 'Smart Interactive Classrooms',
                    'desc'  => 'Every classroom is equipped with interactive whiteboards, projectors, and digital learning tools that make lessons visual, engaging, and memorable for young learners.',
                ],
                [
                    'icon'  => '📚',
                    'title' => 'Well-Equipped Junior Library',
                    'desc'  => 'A dedicated Junior Library stocked with age-appropriate picture books, early readers, activity books, and illustrated encyclopaedias to foster a love for reading from Day 1.',
                ],
                [
                    'icon'  => '🎵',
                    'title' => 'Music & Activity Rooms',
                    'desc'  => 'Dedicated music room with instruments (harmonium, tabla, guitar, keyboard) and multi-purpose activity room for art, craft, drama, and experiential learning.',
                ],
                [
                    'icon'  => '🛡️',
                    'title' => '360° Safety & Surveillance',
                    'desc'  => 'The entire Junior Wing is under continuous CCTV surveillance. Female security personnel, trained non-teaching staff, and bio-metric access ensure every child\'s safety.',
                ],
                [
                    'icon'  => '🏃',
                    'title' => 'Safe & Fun Play Area',
                    'desc'  => 'Purpose-designed outdoor play area with age-appropriate play equipment, soft flooring, and shade structures where children develop gross motor skills and social skills through play.',
                ],
                [
                    'icon'  => '🧠',
                    'title' => 'World-Class Teaching Pedagogy',
                    'desc'  => 'Catering to multiple intelligences through hands-on activities, group projects, visual aids, storytelling, role play, and logical reasoning tasks based on NCF 2022 & NEP 2020.',
                ],
            ],

            'classes'  => 'Pre-Nursery, Nursery, KG, Class I – V',
            'building' => 'Separate, self-contained building with dedicated entry/exit',

            'cta_primary_label'  => 'Admissions Open — Apply Now',
            'cta_primary_url'    => '/registration',
            'cta_secondary_label' => 'Explore Senior Wing',
            'cta_secondary_url'  => '/senior-wing-school-in-panipat',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'hero_eyebrow',  'label' => 'Eyebrow Tag',   'type' => 'text'],
            ['key' => 'intro',         'label' => 'Intro Text',    'type' => 'textarea'],
            ['key' => 'classes',       'label' => 'Classes Covered','type' => 'text'],
            ['key' => 'cta_primary_label',   'label' => 'Primary CTA Label', 'type' => 'text'],
            ['key' => 'cta_primary_url',     'label' => 'Primary CTA URL',   'type' => 'text'],
            ['key' => 'cta_secondary_label', 'label' => 'Secondary CTA Label','type' => 'text'],
            ['key' => 'cta_secondary_url',   'label' => 'Secondary CTA URL', 'type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.junior-wing-page', $settings)->render();
    }
}
