<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/** Icon grid of campus facilities. */
class FacilitiesWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'facilities';
    }

    public function label(): string
    {
        return 'Facilities (grid)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Infrastructure',
            'heading' => 'World-Class Facilities',
            'sub'     => 'Everything a growing mind needs to learn, play and thrive.',
            'items'   => [
                ['label' => 'Smart Classrooms', 'text' => 'Tech-enabled, airy and engaging learning spaces.', 'icon' => 'M3 5h18v11H3zM3 16l-1 3h20l-1-3M9 9h6'],
                ['label' => 'Science Labs', 'text' => 'Fully-equipped Physics, Chemistry and Biology labs.', 'icon' => 'M9 3v6l-5 9a2 2 0 0 0 2 3h12a2 2 0 0 0 2-3l-5-9V3M9 3h6'],
                ['label' => 'Library', 'text' => 'A vast collection of books and digital resources.', 'icon' => 'M4 5a2 2 0 0 1 2-2h6v16H6a2 2 0 0 0-2 2V5zM20 5a2 2 0 0 0-2-2h-6v16h6a2 2 0 0 1 2 2V5z'],
                ['label' => 'Sports', 'text' => 'Expansive grounds and courts for every sport.', 'icon' => 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18'],
                ['label' => 'Transport', 'text' => 'GPS-enabled, safe and reliable bus fleet.', 'icon' => 'M3 6h18v8H3zM3 14l1 4h2l1-2h10l1 2h2l1-4M7 18a1.5 1.5 0 1 0 0-.01M17 18a1.5 1.5 0 1 0 0-.01'],
                ['label' => 'Safety & Security', 'text' => 'CCTV surveillance and trained staff campus-wide.', 'icon' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4'],
                ['label' => 'Activity Rooms', 'text' => 'Dedicated spaces for music, dance and art.', 'icon' => 'M9 18V5l12-2v13M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM21 16a3 3 0 1 1-6 0 3 3 0 0 1 6 0z'],
                ['label' => 'Infirmary', 'text' => 'On-campus medical care and a qualified nurse.', 'icon' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM12 8v6M9 11h6'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $cards = '';
        $i = 0;
        foreach ((array) $this->setting($settings, 'items', []) as $item) {
            $label = $this->e($item['label'] ?? '');
            $text  = $this->e($item['text'] ?? '');
            $path  = $this->e($item['icon'] ?? '');
            $cards .= '<div class="fac" data-reveal data-reveal-delay="' . (($i % 4) + 1) . '">'
                . '<div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="' . $path . '"/></svg></div>'
                . '<h4>' . $label . '</h4><p>' . $text . '</p></div>';
            $i++;
        }

        return $head . '<div class="fac-grid">' . $cards . '</div>';
    }
}
