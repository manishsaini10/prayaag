<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Labs Page Widget — Science, Computer, Robotics & AI Labs Showcase
 * Features: Hero, Lab Cards, Equipment Highlights, Photo Gallery, Achievement Badges
 */
class LabsPageWidget extends AbstractWidget
{
    public function type(): string { return 'labs-page'; }
    public function label(): string { return 'Labs & STEM Centres (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'State-of-the-Art Laboratories',
            'hero_subtitle' => 'Prayaag International School is equipped with cutting-edge laboratories designed to ignite curiosity, foster innovation, and build real-world scientific temperament in every student from Pre-Primary to Class XII.',
            'hero_bg'       => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Science-Lab.webp',

            'labs' => [
                [
                    'icon'  => '🔬',
                    'name'  => 'Physics & Chemistry Laboratory',
                    'desc'  => 'Fully equipped with modern apparatus for experimentation in mechanics, optics, thermodynamics, organic and inorganic chemistry. Compliant with CBSE Class XI–XII practical curriculum.',
                    'badge' => 'Classes VI – XII',
                    'image' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Science-Lab.webp',
                ],
                [
                    'icon'  => '🧬',
                    'name'  => 'Biology Laboratory',
                    'desc'  => 'Equipped with compound and dissecting microscopes, biological specimens, charts, and models supporting NCERT and CBSE practical curriculum for Classes IX–XII.',
                    'badge' => 'Classes IX – XII',
                    'image' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Science-Lab.webp',
                ],
                [
                    'icon'  => '💻',
                    'name'  => 'Computer Science & IT Lab',
                    'desc'  => '100-seat air-conditioned lab with high-speed internet, latest Core i5/i7 systems, licensed Microsoft Office, Python/C++ IDEs, and dedicated server infrastructure for advanced programming.',
                    'badge' => 'Classes III – XII',
                    'image' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Computer-lab.webp',
                ],
                [
                    'icon'  => '🤖',
                    'name'  => 'Robotics & AI Innovation Lab',
                    'desc'  => 'Purpose-built robotics studio featuring Arduino kits, LEGO Mindstorms, 3D printers, and AI/ML workstations where students build, code, and compete at national-level competitions.',
                    'badge' => 'Classes V – XII',
                    'image' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Computer-lab.webp',
                ],
                [
                    'icon'  => '🎨',
                    'name'  => 'Mathematics Resource Room',
                    'desc'  => 'Interactive Maths Lab with Dienes blocks, geo-boards, fraction kits, graphing tools, and digital Maths software for experiential and concept-based learning.',
                    'badge' => 'Classes I – X',
                    'image' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Science-Lab.webp',
                ],
                [
                    'icon'  => '🌐',
                    'name'  => 'Language & Multimedia Lab',
                    'desc'  => 'Smartboard-equipped language lab with headphone booths, audio-visual tools, pronunciation software, and a digital media studio for English, Hindi, and French language development.',
                    'badge' => 'All Classes',
                    'image' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Computer-lab.webp',
                ],
            ],

            'highlights' => [
                ['icon' => '🏆', 'stat' => '6+', 'label' => 'Specialist Labs'],
                ['icon' => '💻', 'stat' => '100+', 'label' => 'Computer Workstations'],
                ['icon' => '🤖', 'stat' => '3D Printing', 'label' => 'Robotics Studio'],
                ['icon' => '🔒', 'stat' => 'ISO Safe', 'label' => 'Safety Standards'],
            ],

            'safety_note' => 'All laboratories strictly follow CBSE Lab Safety Guidelines. Students are provided with safety gear (gloves, goggles, lab coats) for chemistry and biology practicals. Regular safety drills and equipment maintenance audits are conducted.',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'safety_note',   'label' => 'Safety Note',   'type' => 'textarea'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.labs-page', $settings)->render();
    }
}
