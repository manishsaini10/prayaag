<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Senior Wing Page Widget — CBSE High School Excellence & Holistic Development
 */
class SeniorWingPageWidget extends AbstractWidget
{
    public function type(): string { return 'senior-wing-page'; }
    public function label(): string { return 'Senior Wing School (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Senior Wing School — Shaping Tomorrow\'s Leaders',
            'hero_subtitle' => 'Teenage years play a crucial role in shaping character, vision, and personality. At Prayaag\'s Senior Wing, we provide every instrument needed for students to become confident, responsible, and fearless global citizens.',
            'hero_bg'       => '/storage/media/imported/01KTQWWB8D8FEC6S9QTGZQ9BNN.webp',
            'hero_eyebrow'  => 'Classes VI – XII · CBSE',

            'intro' => 'Education is imparted through Tech-enabled Classrooms, well-enhanced Science, Computer, and Language Laboratories. An amalgamation of modernity, refinement, culture, and discipline is what we impart to our students. Our highly-accomplished teaching staff metamorphoses students into fervent global citizens.',

            'highlights' => [
                ['icon' => '📱', 'stat' => 'Smart',    'label' => 'Tech-Enabled Classes'],
                ['icon' => '🔬', 'stat' => '6+',       'label' => 'Advanced Laboratories'],
                ['icon' => '🏆', 'stat' => '95%+',     'label' => 'Board Results'],
                ['icon' => '🌍', 'stat' => 'Holistic', 'label' => 'Development Focus'],
            ],

            'features' => [
                [
                    'icon'  => '📱',
                    'title' => 'Tech-Enabled Smart Classrooms',
                    'desc'  => 'Every classroom in the Senior Wing features interactive smart boards, digital content delivery, real-time assessments, and AR/VR tools to make learning immersive and effective.',
                ],
                [
                    'icon'  => '🔬',
                    'title' => 'Advanced STEM Laboratories',
                    'desc'  => 'State-of-the-art Physics, Chemistry, Biology, Computer Science, Robotics, and AI labs support deep practical learning aligned with CBSE curriculum requirements.',
                ],
                [
                    'icon'  => '🎓',
                    'title' => 'Expert & Accomplished Faculty',
                    'desc'  => 'A team of highly qualified, experienced, and continuously trained teachers guide students through Classes VI–XII with subject mastery and mentoring excellence.',
                ],
                [
                    'icon'  => '📊',
                    'title' => 'CBSE Board Excellence',
                    'desc'  => 'Comprehensive board exam preparation through structured revision, mock tests, doubt-clearing sessions, and individual academic counseling ensures consistently outstanding results.',
                ],
                [
                    'icon'  => '🌍',
                    'title' => 'Experiential & Global Learning',
                    'desc'  => 'Beyond rigid classroom structures, students engage in industry visits, international exchange programs, Model UN, hackathons, and research projects for real-world readiness.',
                ],
                [
                    'icon'  => '🧘',
                    'title' => 'Holistic Well-Being & Life Skills',
                    'desc'  => 'Mental wellness sessions, sports coaching, performing arts, personality development workshops, and career counseling ensure students\' holistic growth and emotional resilience.',
                ],
            ],

            'streams' => [
                ['name' => 'Science Stream',   'subs' => 'Physics, Chemistry, Biology / Maths, CS/IP'],
                ['name' => 'Commerce Stream',  'subs' => 'Accountancy, Business Studies, Economics, Maths'],
                ['name' => 'Humanities Stream','subs' => 'History, Geography, Political Science, Psychology, Fine Arts'],
            ],

            'cta_primary_label'   => 'Admissions Open — Apply Now',
            'cta_primary_url'     => '/registration',
            'cta_secondary_label' => 'Explore Junior Wing',
            'cta_secondary_url'   => '/junior-wing-school-in-panipat',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'hero_eyebrow',  'label' => 'Eyebrow Tag',   'type' => 'text'],
            ['key' => 'intro',         'label' => 'Intro Text',    'type' => 'textarea'],
            ['key' => 'cta_primary_label',   'label' => 'Primary CTA Label',  'type' => 'text'],
            ['key' => 'cta_primary_url',     'label' => 'Primary CTA URL',    'type' => 'text'],
            ['key' => 'cta_secondary_label', 'label' => 'Secondary CTA Label','type' => 'text'],
            ['key' => 'cta_secondary_url',   'label' => 'Secondary CTA URL',  'type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.senior-wing-page', $settings)->render();
    }
}
