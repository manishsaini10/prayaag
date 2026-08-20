<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Modular Media Category Gallery Widget.
 * Can render any specific campus life category (Dance & Music, Sports, Arts & Craft, Fun Activities).
 */
class MediaGalleryWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'media-gallery';
    }

    public function label(): string
    {
        return 'Media Category Gallery';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultSettings(): array
    {
        return [
            'category' => 'all', // 'dance_music', 'sports', 'arts_craft', 'fun_activities', 'all'
            'columns'  => 3,
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'category' => [
                'type'    => 'select',
                'label'   => 'Gallery Category',
                'default' => 'all',
                'options' => [
                    'all'            => 'All Categories Combined',
                    'dance_music'    => 'Dance & Music',
                    'sports'         => 'Sports & Athletics',
                    'arts_craft'     => 'Arts & Craft',
                    'fun_activities' => 'Fun Activities & Play',
                ],
            ],
            'columns' => [
                'type'    => 'select',
                'label'   => 'Columns',
                'default' => 3,
                'options' => [
                    2 => '2 Columns',
                    3 => '3 Columns',
                    4 => '4 Columns',
                ],
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
