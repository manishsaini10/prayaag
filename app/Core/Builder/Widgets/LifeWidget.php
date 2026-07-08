<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * "Life at school" gradient tiles. Defaults carry the existing home-page tiles.
 */
class LifeWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'life';
    }

    public function label(): string
    {
        return 'Life at School (tiles)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Life at Prayaag',
            'heading' => 'Celebrating Every Moment',
            'sub'     => 'We celebrate the tiniest ounce of happiness in the grandest way possible.',
            'items'   => [
                ['label' => 'Dance', 'url' => '/media/', 'image' => ''],
                ['label' => 'Sports', 'url' => '/media/', 'image' => ''],
                ['label' => 'Fun Activities', 'url' => '/media/', 'image' => ''],
                ['label' => 'Art & Craft', 'url' => '/media/', 'image' => ''],
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

        $tiles = '';
        $i = 0;
        foreach ((array) $this->setting($settings, 'items', []) as $item) {
            $variant = ($i % 4) + 1;
            $delay = ($i % 4) + 1;
            $i++;
            $label = $this->e($item['label'] ?? '');
            $url   = $this->e($item['url'] ?? '#');
            $img   = $item['image'] ?? '';
            $style = $img ? ' style="background-image:url(\'' . $this->e($img) . '\');background-size:cover;background-position:center"' : '';
            $tiles .= '<a class="life life-' . $variant . '"' . $style . ' href="' . $url . '" data-reveal data-reveal-delay="' . $delay . '">'
                . '<div class="inner"><h4>' . $label . '</h4></div></a>';
        }

        return $head . '<div class="life-grid">' . $tiles . '</div>';
    }
}
