<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Builder\Concerns\RendersCollection;
use App\Models\Event;
use Illuminate\Support\Str;

/**
 * Dynamic widget: Upcoming Events with Multi-Category Support & Interactive Filter Tabs.
 * Supports filtering by single or multiple categories in Page Builder settings.
 */
class UpcomingEventsWidget extends AbstractWidget
{
    use RendersCollection;

    public function type(): string
    {
        return 'upcoming_events';
    }

    public function label(): string
    {
        return 'Upcoming Events';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return array_merge($this->collectionDefaults('timeline', 6), [
            'eyebrow'      => 'School Calendar & Distinctions',
            'heading'      => 'Upcoming Events & Celebrations',
            'categories'   => ['all'],
            'show_filter'  => true,
            'show_badge'   => true,
        ]);
    }

    public function fieldOptions(): array
    {
        return [
            'layout'     => $this->collectionLayouts(),
            'categories' => array_merge(['all' => 'All Categories'], Event::CATEGORIES),
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $limit      = max(1, (int) $this->setting($settings, 'limit', 6));
        $showFilter = (bool) ($settings['show_filter'] ?? true);
        $showBadge  = (bool) ($settings['show_badge'] ?? true);

        // Parse selected categories (can be array or comma-separated string)
        $rawCategories = $settings['categories'] ?? $settings['category'] ?? ['all'];
        if (is_string($rawCategories)) {
            $rawCategories = array_map('trim', explode(',', $rawCategories));
        } elseif (!is_array($rawCategories)) {
            $rawCategories = [$rawCategories];
        }

        $query = Event::upcoming()->orderBy('starts_at');

        // Apply category filter if not 'all'
        $filteredCats = array_filter($rawCategories, fn ($c) => !empty($c) && strtolower($c) !== 'all');
        if (!empty($filteredCats)) {
            $query->whereIn('category', $filteredCats);
        }

        $events = $query->limit($limit)->get();

        $icon = $this->collectionIcon('calendar');
        $items = [];
        $distinctCategories = [];

        foreach ($events as $event) {
            $image    = (string) ($event->image ?? $event->cover ?? $event->banner ?? '');
            $desc     = (string) ($event->excerpt ?? $event->description ?? '');
            if ($desc !== '') {
                $desc = Str::limit(trim(strip_tags($desc)), 130);
            }
            $location = (string) ($event->location ?? $event->venue ?? '');
            $slug     = (string) ($event->slug ?? '');
            $cat      = (string) ($event->category ?: 'General');

            if (!in_array($cat, $distinctCategories, true)) {
                $distinctCategories[] = $cat;
            }

            $dateFormatted = optional($event->starts_at)->format('D, M j') ?: '';
            $timeFormatted = optional($event->starts_at)->format('g:i A') ?: '';

            $eyebrowText = $dateFormatted;
            if ($showBadge) {
                $eyebrowText = "🏷️ {$cat}" . ($dateFormatted ? " · {$dateFormatted}" : '');
            }

            $items[] = [
                'image'    => $image,
                'icon'     => $image === '' ? $icon : '',
                'eyebrow'  => $eyebrowText,
                'title'    => (string) $event->title,
                'text'     => $desc,
                'meta'     => trim($location . (($location && $timeFormatted) ? ' · ' : '') . $timeFormatted),
                'href'     => $slug !== '' ? '/' . $this->e($slug) : '',
                'category' => Str::slug($cat),
                'cat_name' => $cat,
            ];
        }

        $renderedHtml = $this->renderCollectionWithCategories($items, $settings, [
            'accent'             => '#0e7490',
            'default'            => 'timeline',
            'show_filter'        => $showFilter && count($distinctCategories) > 1,
            'categories'         => $distinctCategories,
        ]);

        return $renderedHtml;
    }

    /**
     * Custom collection renderer with interactive multi-category filter tabs.
     */
    protected function renderCollectionWithCategories(array $items, array $settings, array $opts = []): string
    {
        $layout = (string) ($settings['layout'] ?? ($opts['default'] ?? 'timeline'));
        if (! isset(self::COLLECTION_LAYOUTS[$layout])) {
            $layout = 'timeline';
        }
        $accent = (string) ($opts['accent'] ?? '#0e7490');

        $head = $this->sectionHead(
            (string) ($settings['eyebrow'] ?? ''),
            (string) ($settings['heading'] ?? ''),
            (string) ($settings['sub'] ?? '')
        );

        if (empty($items)) {
            return $head . '<div class="fx fx--' . $layout . ' pb-empty"><p style="text-align:center;color:#64748b;padding:30px">No upcoming events found in selected categories.</p></div>';
        }

        $widgetId = 'ev_widget_' . substr(md5(uniqid('', true)), 0, 8);

        // Build Category Filter Bar if enabled
        $filterBarHtml = '';
        if (!empty($opts['show_filter']) && !empty($opts['categories'])) {
            $filterBtns = '<button type="button" class="ev-filter-btn active" data-cat="all" onclick="filterEventsTab(\'' . $widgetId . '\', \'all\', this)">All (' . count($items) . ')</button>';
            foreach ($opts['categories'] as $catName) {
                $slug = Str::slug($catName);
                $count = count(array_filter($items, fn($it) => ($it['category'] ?? '') === $slug));
                $filterBtns .= '<button type="button" class="ev-filter-btn" data-cat="' . $slug . '" onclick="filterEventsTab(\'' . $widgetId . '\', \'' . $slug . '\', this)">' . $this->e($catName) . ' (' . $count . ')</button>';
            }

            $filterBarHtml = <<<HTML
            <div class="ev-filter-bar" id="filter_{$widgetId}">
                {$filterBtns}
            </div>
            HTML;
        }

        $cards = '';
        $i = 0;
        foreach ($items as $it) {
            $image    = (string) ($it['image'] ?? '');
            $icon     = (string) ($it['icon'] ?? '');
            $eyebrow  = (string) ($it['eyebrow'] ?? '');
            $title    = (string) ($it['title'] ?? '');
            $text     = (string) ($it['text'] ?? '');
            $meta     = (string) ($it['meta'] ?? '');
            $href     = (string) ($it['href'] ?? '');
            $catSlug  = (string) ($it['category'] ?? 'all');
            $catName  = (string) ($it['cat_name'] ?? '');

            // Media
            $media = '';
            if ($image !== '') {
                $media = '<a class="fx-media" href="' . $this->e($href ?: '#') . '" style="background-image:url(\'' . $this->e($image) . '\')" aria-label="' . $this->e($title) . '"></a>';
            } elseif ($icon !== '') {
                $media = '<div class="fx-ic">' . $icon . '</div>';
            }

            // Body
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

            $reveal = $layout === 'slider' ? '' : ' data-reveal data-reveal-delay="' . (($i % 3) + 1) . '"';

            $cards .= '<article class="fx-item ev-card-item" data-evcat="' . $catSlug . '"' . $reveal . '>' . $media . $body . '</article>';
            $i++;
        }

        $styles = <<<HTML
        <style>
        .ev-filter-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            margin: 0 auto 28px;
            max-width: 100%;
            padding: 0 16px;
        }
        .ev-filter-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            color: #475569;
            padding: 7px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        .ev-filter-btn:hover {
            border-color: #0b2545;
            color: #0b2545;
            transform: translateY(-1px);
        }
        .ev-filter-btn.active {
            background: #0b2545;
            border-color: #0b2545;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(11,37,69,0.25);
        }
        .ev-card-item.ev-hidden {
            display: none !important;
        }
        </style>
        HTML;

        $script = <<<HTML
        <script>
        if (typeof window.filterEventsTab === 'undefined') {
            window.filterEventsTab = function(widgetId, category, btn) {
                const container = document.getElementById(widgetId);
                const filterBar = document.getElementById('filter_' + widgetId);
                if (!container) return;

                if (filterBar) {
                    filterBar.querySelectorAll('.ev-filter-btn').forEach(b => b.classList.remove('active'));
                    if (btn) btn.classList.add('active');
                }

                const items = container.querySelectorAll('.ev-card-item');
                items.forEach(card => {
                    const cardCat = card.getAttribute('data-evcat');
                    if (category === 'all' || cardCat === category) {
                        card.classList.remove('ev-hidden');
                        card.style.opacity = '0';
                        setTimeout(() => { card.style.opacity = '1'; card.style.transition = 'opacity .3s ease'; }, 10);
                    } else {
                        card.classList.add('ev-hidden');
                    }
                });
            };
        }
        </script>
        HTML;

        return $styles
            . $head
            . $filterBarHtml
            . '<div class="fx fx--' . $layout . '" id="' . $widgetId . '" style="--fx-accent:' . $accent . '">'
            . '<div class="fx-list">' . $cards . '</div></div>'
            . $script;
    }
}
