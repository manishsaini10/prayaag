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
        return [];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.media-page')->render();
    }
}
