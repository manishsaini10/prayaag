<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page Media ("Life at Prayaag") Widget.
 * Renders complete campus life gallery — Dance & Music, Sports & Athletics,
 * Arts & Craft, Fun Activities, and Auto-playing Newspaper Press Clippings Slider with Lightbox.
 */
class MediaPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'media-page';
    }

    public function label(): string
    {
        return 'Media Page (Life at Prayaag Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'autoplay'        => true,
            'interval'        => 3000, // 3000ms = 3s interval
            'animation_speed' => 600,  // 600ms ultra-smooth easing
            'pause_on_hover'  => true,
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'autoplay' => [
                'type'    => 'boolean',
                'label'   => 'Auto Play News Slider',
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
                'min'     => 200,
                'max'     => 2000,
                'step'    => 100,
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
        $mergedSettings = array_merge($this->defaultSettings(), $settings);

        return view('widgets.media-page', [
            'settings' => $mergedSettings,
        ])->render();
    }
}
