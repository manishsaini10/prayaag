<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Quick-access tile grid that overlaps the hero. Each tile is an icon (SVG
 * path), a label and a link. Defaults carry the existing home-page tiles.
 */
class QuickLinksWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'quick-links';
    }

    public function label(): string
    {
        return 'Quick Links (tiles)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'items' => [
                ['label' => 'School Trip', 'url' => '/tours-and-excursions/', 'icon' => 'M3 7h13l3 4v6h-3a2 2 0 0 1-4 0H8a2 2 0 0 1-4 0H3V7z'],
                ['label' => 'Labs', 'url' => '/labs/', 'icon' => 'M9 3v6l-5 9a2 2 0 0 0 2 3h12a2 2 0 0 0 2-3l-5-9V3M9 3h6M7.5 14h9'],
                ['label' => 'Sports', 'url' => '/sports/', 'icon' => 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18'],
                ['label' => 'Library', 'url' => '/library/', 'icon' => 'M4 5a2 2 0 0 1 2-2h6v16H6a2 2 0 0 0-2 2V5zM20 5a2 2 0 0 0-2-2h-6v16h6a2 2 0 0 1 2 2V5z'],
                ['label' => 'Classroom', 'url' => '/classrooms/', 'icon' => 'M3 5h18v11H3zM3 16l-1 3h20l-1-3M9 9h6'],
                ['label' => 'Safety & Security', 'url' => '/safety-security/', 'icon' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4'],
                ['label' => 'Transportation', 'url' => '/transportations/', 'icon' => 'M3 6h18v8H3zM3 14l1 4h2l1-2h10l1 2h2l1-4M7 18a1.5 1.5 0 1 0 0-.01M17 18a1.5 1.5 0 1 0 0-.01'],
                ['label' => 'UNESCO', 'url' => '/unesco/', 'icon' => 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3v18'],
                ['label' => 'Events', 'url' => '/events/', 'icon' => 'M3 5h18v16H3zM3 9h18M8 3v4M16 3v4'],
                ['label' => 'Photo Gallery', 'url' => '/media/', 'icon' => 'M3 5h18v14H3zM3 15l5-5 4 4 3-3 6 6'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $items = $this->setting($settings, 'items', []);
        if (! is_array($items)) {
            $items = [];
        }

        $cards = '';
        $i = 0;
        foreach ($items as $item) {
            $label = $this->e($item['label'] ?? '');
            $url   = $this->e($item['url'] ?? '#');
            $path  = $this->e($item['icon'] ?? '');
            $delay = ($i % 6) + 1;
            $i++;
            $cards .= '<a class="qcard" href="' . $url . '" data-reveal data-reveal-delay="' . $delay . '">'
                . '<span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="' . $path . '"/></svg></span>'
                . '<span>' . $label . '</span></a>';
        }

        return '<div class="container quick-wrap"><div class="quick-grid">' . $cards . '</div></div>';
    }
}
