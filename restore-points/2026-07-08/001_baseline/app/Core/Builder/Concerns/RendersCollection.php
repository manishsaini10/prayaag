<?php

namespace App\Core\Builder\Concerns;

/**
 * Shared "collection" rendering engine for dynamic list widgets
 * (Posts, Notices, Events, Downloads, Gallery, …).
 *
 * A widget maps its model rows to a normalised array of items, then calls
 * renderCollection(). The chosen "layout" setting (a dropdown in the Page
 * Builder) decides the design — 8 premium variants share one CSS system (.fx).
 *
 * Item shape (all keys optional except title or image):
 *   ['image' => url, 'icon' => svg, 'eyebrow' => str, 'title' => str,
 *    'text' => str, 'meta' => str, 'href' => url]
 */
trait RendersCollection
{
    /** value => label */
    public const COLLECTION_LAYOUTS = [
        'grid'     => 'Grid (image cards)',
        'list'     => 'List (media rows)',
        'slider'   => 'Slider (swipe)',
        'masonry'  => 'Masonry',
        'timeline' => 'Timeline',
        'magazine' => 'Magazine (featured)',
        'overlay'  => 'Overlay cards',
        'minimal'  => 'Minimal (text)',
    ];

    /** @return array<int, string> */
    protected function collectionLayouts(): array
    {
        return array_keys(self::COLLECTION_LAYOUTS);
    }

    /**
     * Common settings every collection widget should expose. Merge into
     * defaultSettings(): array_merge($this->collectionDefaults('grid'), [...]).
     */
    protected function collectionDefaults(string $layout = 'grid', int $limit = 6): array
    {
        return [
            'layout'  => $layout,
            'limit'   => $limit,
            'eyebrow' => '',
            'heading' => '',
            'sub'     => '',
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $items
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $opts   ['accent' => css color, 'default' => layout]
     */
    protected function renderCollection(array $items, array $settings, array $opts = []): string
    {
        $layout = (string) ($settings['layout'] ?? ($opts['default'] ?? 'grid'));
        if (! isset(self::COLLECTION_LAYOUTS[$layout])) {
            $layout = 'grid';
        }
        $accent = (string) ($opts['accent'] ?? 'var(--gold)');

        $head = $this->sectionHead(
            (string) ($settings['eyebrow'] ?? ''),
            (string) ($settings['heading'] ?? ''),
            (string) ($settings['sub'] ?? '')
        );

        if (empty($items)) {
            return $head . '<div class="fx fx--' . $layout . ' pb-empty"></div>';
        }

        $cards = '';
        $i = 0;
        foreach ($items as $it) {
            $image   = (string) ($it['image'] ?? '');
            $icon    = (string) ($it['icon'] ?? '');
            $eyebrow = (string) ($it['eyebrow'] ?? '');
            $title   = (string) ($it['title'] ?? '');
            $text    = (string) ($it['text'] ?? '');
            $meta    = (string) ($it['meta'] ?? '');
            $href    = (string) ($it['href'] ?? '');

            // Media (image preferred, else icon block)
            $media = '';
            if ($image !== '') {
                $media = '<a class="fx-media" href="' . $this->e($href ?: '#') . '" style="background-image:url(\'' . $this->e($image) . '\')" aria-label="' . $this->e($title) . '"></a>';
            } elseif ($icon !== '') {
                $media = '<div class="fx-ic">' . $icon . '</div>';
            }

            // Body (only render parts that exist)
            $bodyInner = '';
            if ($eyebrow !== '') {
                $bodyInner .= '<span class="fx-eyebrow">' . $this->e($eyebrow) . '</span>';
            }
            if ($title !== '') {
                $titleHtml = $href !== ''
                    ? '<a href="' . $this->e($href) . '">' . $this->e($title) . '</a>'
                    : $this->e($title);
                $bodyInner .= '<h3 class="fx-title">' . $titleHtml . '</h3>';
            }
            if ($text !== '') {
                $bodyInner .= '<p class="fx-text">' . $this->e($text) . '</p>';
            }
            if ($meta !== '') {
                $bodyInner .= '<div class="fx-meta">' . $this->e($meta) . '</div>';
            }
            $body = $bodyInner !== '' ? '<div class="fx-body">' . $bodyInner . '</div>' : '';

            // Slider items scroll off-screen, so skip the scroll-reveal there.
            $reveal = $layout === 'slider' ? '' : ' data-reveal data-reveal-delay="' . (($i % 3) + 1) . '"';

            $cards .= '<article class="fx-item"' . $reveal . '>' . $media . $body . '</article>';
            $i++;
        }

        return $head
            . '<div class="fx fx--' . $layout . '" style="--fx-accent:' . $accent . '">'
            . '<div class="fx-list">' . $cards . '</div></div>';
    }

    /** A few inline SVG icons widgets can use when there's no image. */
    protected function collectionIcon(string $name): string
    {
        $icons = [
            'doc'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>',
            'bell'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>',
            'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
            'news'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h5"/></svg>',
        ];

        return $icons[$name] ?? '';
    }
}
