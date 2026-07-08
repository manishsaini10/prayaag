<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Instagram "glimpses" grid + follow button. Defaults carry the existing
 * home-page posts.
 */
class GlimpsesWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'glimpses';
    }

    public function label(): string
    {
        return 'Glimpses (Instagram)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow'      => '@prayaag2016',
            'heading'      => 'Glimpses of Prayaag',
            'follow_label' => 'Follow on Instagram',
            'follow_url'   => 'https://www.instagram.com/prayaag2016/',
            'items'        => [
                ['caption' => 'Confidence grows when little voices are heard — a fun-filled Show and Tell Competition for Grade II.', 'url' => 'https://www.instagram.com/reel/DZHqOv6sraX/', 'image' => ''],
                ['caption' => 'Science faculty of the vicinity attended the CBSE Science Workshop at Prayaag International School.', 'url' => 'https://www.instagram.com/p/DZE4rOxDwlz/', 'image' => ''],
                ['caption' => 'Too cute to handle, too fun to miss! Cake vibes, happy tribe, and tiny smiles all day long.', 'url' => 'https://www.instagram.com/p/DZCWLHUDwCR/', 'image' => ''],
                ['caption' => '“Science is the key to endless possibilities.” Young minds at the Science Quiz Competition.', 'url' => 'https://www.instagram.com/reel/DY9jPe9Mv_V/', 'image' => ''],
                ['caption' => 'Eid Mubarak! Wishing our entire school family a joyful celebration filled with togetherness.', 'url' => 'https://www.instagram.com/p/DY3mRgTj-oB/', 'image' => ''],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead($this->setting($settings, 'eyebrow'), $this->setting($settings, 'heading'));

        $svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>';

        $cards = '';
        $i = 0;
        foreach ((array) $this->setting($settings, 'items', []) as $item) {
            $cap = $this->e($item['caption'] ?? '');
            $url = $this->e($item['url'] ?? '#');
            $img = $item['image'] ?? '';
            $ph  = $img ? '<img src="' . $this->e($img) . '" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover">' : $svg;
            $delay = ($i % 5) + 1;
            $i++;
            $cards .= '<a class="ig" href="' . $url . '" target="_blank" rel="noopener" data-reveal data-reveal-delay="' . $delay . '">'
                . '<div class="ph">' . $ph . '</div><div class="cap">' . $cap . '</div></a>';
        }

        $follow = '';
        if ($label = $this->setting($settings, 'follow_label')) {
            $follow = '<div style="text-align:center;margin-top:30px"><a class="btn btn-navy" href="'
                . $this->e($this->setting($settings, 'follow_url', '#')) . '" target="_blank" rel="noopener">' . $this->e($label) . '</a></div>';
        }

        return $head . '<div class="ig-grid">' . $cards . '</div>' . $follow;
    }
}
