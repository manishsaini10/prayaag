<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * YouTube video grid. Defaults carry the existing home-page channel videos.
 * Dynamic-ish only in that it embeds third-party iframes; no DB queries.
 */
class VideosWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'videos';
    }

    public function label(): string
    {
        return 'Video Grid';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Watch',
            'heading' => 'From Our Channel',
            'items'   => [
                'https://www.youtube.com/embed/uF-rgUjsTEE',
                'https://www.youtube.com/embed/R1RxRZRUEa0',
                'https://www.youtube.com/embed/JdZbM6x7Y8s',
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead($this->setting($settings, 'eyebrow'), $this->setting($settings, 'heading'));

        $vids = '';
        $i = 0;
        foreach ((array) $this->setting($settings, 'items', []) as $url) {
            $src = $this->e(is_array($url) ? ($url['url'] ?? '') : $url);
            if (! $src) {
                continue;
            }
            $delay = ($i % 3) + 1;
            $i++;
            $vids .= '<div class="vid" data-reveal data-reveal-delay="' . $delay . '">'
                . '<iframe src="' . $src . '" title="Video" loading="lazy" allowfullscreen></iframe></div>';
        }

        return $head . '<div class="vid-grid">' . $vids . '</div>';
    }
}
