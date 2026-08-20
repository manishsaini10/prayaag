<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Dedicated News & Press Clippings Carousel Slider Widget.
 * Can be placed into any page/section in the Page Builder.
 */
class NewsSliderWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'news-slider';
    }

    public function label(): string
    {
        return 'News & Press Slider (17 Clippings)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'autoplay'        => true,
            'interval'        => 3000,
            'animation_speed' => 600,
            'pause_on_hover'  => true,
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'autoplay' => [
                'type'    => 'boolean',
                'label'   => 'Auto Play',
                'default' => true,
            ],
            'interval' => [
                'type'    => 'number',
                'label'   => 'Auto Play Interval (ms)',
                'default' => 3000,
                'min'     => 1000,
                'max'     => 10000,
                'step'    => 500,
            ],
            'animation_speed' => [
                'type'    => 'number',
                'label'   => 'Animation Speed (ms)',
                'default' => 600,
            ],
            'pause_on_hover' => [
                'type'    => 'boolean',
                'label'   => 'Pause on Hover',
                'default' => true,
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.media-page', [
            'settings' => array_merge($this->defaultSettings(), $settings),
        ])->render();
    }
}
