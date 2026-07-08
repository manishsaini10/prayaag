<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use Illuminate\Support\Facades\Blade;

class InstagramWidget extends AbstractWidget
{
    public const LAYOUTS = [
        'grid'      => 'Grid',
        'masonry'   => 'Masonry',
        'carousel'  => 'Carousel (horizontal scroll)',
        'highlight' => 'Featured carousel',
    ];

    public function type(): string
    {
        return 'instagram';
    }

    public function label(): string
    {
        return 'Instagram Feed';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultSettings(): array
    {
        return [
            'layout'          => 'grid',
            'limit'           => 12,
            'columns_desktop' => 4,
            'columns_tablet'  => 3,
            'columns_mobile'  => 2,
            'show_caption'    => true,
            'show_likes'      => true,
            'show_button'     => true,
            'infinite_scroll' => true,
            'filter_type'     => false,
            'heading'         => '',
            'subheading'      => '',
        ];
    }

    public function fieldOptions(): array
    {
        return ['layout' => array_keys(self::LAYOUTS)];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $heading = (string) $this->setting($settings, 'heading', '');
        $subheading = (string) $this->setting($settings, 'subheading', '');

        $html = '';
        if ($heading !== '' || $subheading !== '') {
            $html .= '<div class="tw-section-head">';
            if ($heading !== '') {
                $html .= '<h2 class="tw-heading">' . $this->e($heading) . '</h2>';
            }
            if ($subheading !== '') {
                $html .= '<p class="tw-sub">' . $this->e($subheading) . '</p>';
            }
            $html .= '</div>';
        }

        $attrs = [
            'layout'           => $this->setting($settings, 'layout', 'grid'),
            'limit'            => max(1, (int) $this->setting($settings, 'limit', 12)),
            'columns-desktop'  => max(1, (int) $this->setting($settings, 'columns_desktop', 4)),
            'columns-tablet'   => max(1, (int) $this->setting($settings, 'columns_tablet', 3)),
            'columns-mobile'   => max(1, (int) $this->setting($settings, 'columns_mobile', 2)),
        ];

        if ($this->setting($settings, 'show_caption')) {
            $attrs['show-caption'] = 'true';
        }
        if ($this->setting($settings, 'show_likes')) {
            $attrs['show-likes'] = 'true';
        }
        if ($this->setting($settings, 'show_button')) {
            $attrs['show-button'] = 'true';
        }
        if ($this->setting($settings, 'infinite_scroll')) {
            $attrs['infinite-scroll'] = 'true';
        }
        if ($this->setting($settings, 'filter_type')) {
            $attrs['filter-type'] = 'true';
        }
        if ($this->setting($settings, 'popup')) {
            $attrs['popup'] = 'true';
        }

        $attrStr = '';
        foreach ($attrs as $key => $val) {
            $attrStr .= ' ' . $key . '="' . $this->e((string) $val) . '"';
        }

        $html .= Blade::render('<x-instagram-feed' . $attrStr . ' />');

        return $html;
    }
}
