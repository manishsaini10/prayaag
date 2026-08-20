<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Safety & Security Page Widget — CCTV, Perimeter, Medical & Emergency Systems
 */
class SafetySecurityPageWidget extends AbstractWidget
{
    public function type(): string { return 'safety-security-page'; }
    public function label(): string { return 'Safety & Security (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Campus Safety & Security',
            'hero_subtitle' => 'At Prayaag International School, the safety and well-being of every student, teacher, and staff member is our highest priority. We have built a multi-layered security ecosystem that ensures a protected, caring, and nurturing campus environment.',
            'hero_bg'       => '/storage/media/imported/01KWEAM88E5A97A8HC1H95YAX8.webp',

            'stats' => [
                ['icon' => '📹', 'value' => '200+',    'label' => 'CCTV Cameras'],
                ['icon' => '👮', 'value' => '24×7',    'label' => 'Security Personnel'],
                ['icon' => '🏥', 'value' => 'On-Site', 'label' => 'Medical Infirmary'],
                ['icon' => '🚨', 'value' => 'Zero',    'label' => 'Security Incidents'],
            ],

            'features' => [
                [
                    'icon'  => '📹',
                    'title' => 'Comprehensive CCTV Surveillance',
                    'desc'  => 'Over 200 high-resolution CCTV cameras strategically installed at all entrances, classrooms, corridors, amphitheatre, swimming pool, common areas, and parking lots. Footage is monitored live and stored for 30 days.',
                ],
                [
                    'icon'  => '🚧',
                    'title' => 'Secure Perimeter & Controlled Access',
                    'desc'  => 'High-security fences and electronically controlled gates surround the entire school boundary. Entry and exit is strictly controlled through identity verification. Visitors must register at the gate.',
                ],
                [
                    'icon'  => '👮',
                    'title' => 'Trained Security Personnel',
                    'desc'  => 'A dedicated team of trained male and female security guards patrols the campus round-the-clock, ensuring a secure environment at all times including evenings and weekends.',
                ],
                [
                    'icon'  => '🏥',
                    'title' => 'Medical Infirmary & First Aid',
                    'desc'  => 'A fully-equipped medical room with a qualified nurse is available on campus during school hours. First Aid kits are stationed in every building. Ambulance tie-up is in place for emergencies.',
                ],
                [
                    'icon'  => '🚨',
                    'title' => 'Emergency Response Systems',
                    'desc'  => 'Campus-wide intercom, public address (PA) system, and fire alarm systems ensure instant communication during any emergency. Regular fire drills and emergency mock exercises are conducted.',
                ],
                [
                    'icon'  => '🙋',
                    'title' => 'Female Security & Child Safety',
                    'desc'  => 'Dedicated female security guards are posted for supervision of girl students, washrooms, and changing rooms. The school strictly follows POCSO Act guidelines for child safety and protection.',
                ],
            ],

            'policy_note' => 'Prayaag International School strictly adheres to CBSE Child Safety Guidelines, POCSO Act 2012, and Haryana Government School Safety Norms. All staff undergo mandatory background verification and child safety training before joining.',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'policy_note',   'label' => 'Policy Note',   'type' => 'textarea'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.safety-security-page', $settings)->render();
    }
}
