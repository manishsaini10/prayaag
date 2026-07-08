<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Builder\Concerns\RendersCollection;
use App\Models\Download;

/**
 * Dynamic widget: published downloads (optionally filtered by category),
 * in 8 premium layouts.
 */
class DownloadsWidget extends AbstractWidget
{
    use RendersCollection;

    public function type(): string
    {
        return 'downloads';
    }

    public function label(): string
    {
        return 'Downloads';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return array_merge($this->collectionDefaults('list', 20), ['category' => '']);
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
        $limit = max(1, (int) $this->setting($settings, 'limit', 20));
        $category = (string) $this->setting($settings, 'category', '');

        $query = Download::published()->orderBy('sort_order');
        if ($category !== '') {
            $query->where('category', $category);
        }
        $downloads = $query->limit($limit)->get();

        $icon = $this->collectionIcon('doc');
        $items = [];
        foreach ($downloads as $d) {
            $type = strtoupper((string) ($d->file_type ?? ''));

            $items[] = [
                'icon'    => $icon,
                'eyebrow' => $type !== '' ? $type : '',
                'title'   => (string) $d->title,
                'text'    => (string) ($d->description ?? ''),
                'meta'    => (string) ($d->category ?? ''),
                'href'    => (string) ($d->file ?? ''),
            ];
        }

        return $this->renderCollection($items, $settings, ['accent' => '#4f46e5', 'default' => 'list']);
    }
}
