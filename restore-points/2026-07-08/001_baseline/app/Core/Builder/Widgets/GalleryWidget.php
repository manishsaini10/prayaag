<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Builder\Concerns\RendersCollection;
use App\Models\Gallery;

/**
 * Dynamic widget: a published gallery's images by slug, in 8 premium layouts.
 */
class GalleryWidget extends AbstractWidget
{
    use RendersCollection;

    public function type(): string
    {
        return 'gallery';
    }

    public function label(): string
    {
        return 'Gallery';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultSettings(): array
    {
        return array_merge($this->collectionDefaults('masonry', 24), ['slug' => '']);
    }

    public function fieldOptions(): array
    {
        return ['layout' => $this->collectionLayouts()];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $slug = (string) $this->setting($settings, 'slug', '');
        if ($slug === '') {
            return $this->renderCollection([], $settings, ['accent' => 'var(--navy-3)', 'default' => 'masonry']);
        }

        $limit = max(1, (int) $this->setting($settings, 'limit', 24));
        $gallery = Gallery::published()->where('slug', $slug)->with('images')->first();

        $items = [];
        if ($gallery) {
            foreach ($gallery->images->take($limit) as $image) {
                $url = (string) $image->image;
                $items[] = [
                    'image' => $url,
                    'title' => (string) ($image->caption ?? ''),
                    'href'  => $url,
                ];
            }
        }

        return $this->renderCollection($items, $settings, ['accent' => 'var(--navy-3)', 'default' => 'masonry']);
    }
}
