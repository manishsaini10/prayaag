<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Animated statistics / counters. Numbers count up when scrolled into view
 * (see public/site.js). Default figures are placeholders — edit them in the
 * builder to your school's real numbers before publishing.
 */
class StatsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'stats';
    }

    public function label(): string
    {
        return 'Statistics / Counters';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'By the Numbers',
            'heading' => 'Our Journey in Figures',
            'items'   => [
                ['value' => 2016, 'suffix' => '', 'label' => 'Established'],
                ['value' => 2000, 'suffix' => '+', 'label' => 'Happy Students'],
                ['value' => 100, 'suffix' => '+', 'label' => 'Qualified Faculty'],
                ['value' => 30, 'suffix' => '+', 'label' => 'Awards & Honours'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead($this->setting($settings, 'eyebrow'), $this->setting($settings, 'heading'));

        $stats = '';
        $i = 0;
        foreach ((array) $this->setting($settings, 'items', []) as $s) {
            $value  = (float) ($s['value'] ?? 0);
            $suffix = $this->e($s['suffix'] ?? '');
            $label  = $this->e($s['label'] ?? '');
            $delay  = ($i % 4) + 1;
            $i++;
            $stats .= '<div class="stat" data-reveal data-reveal-delay="' . $delay . '">'
                . '<div class="num" data-count="' . $value . '" data-suffix="' . $suffix . '">0' . $suffix . '</div>'
                . '<div class="lbl">' . $label . '</div></div>';
        }

        return $head . '<div class="stats-grid">' . $stats . '</div>';
    }
}
