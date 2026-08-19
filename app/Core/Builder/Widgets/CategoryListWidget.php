<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Post Category List Grid.
 * Blog news category cards with item counter badges & icons.
 */
class CategoryListWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-category-list';
    }

    public function label(): string
    {
        return 'Post Category List Grid';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'News Categories',
            'heading' => 'Browse News & Updates by Subject',
            'cats'    => [
                ['name' => 'Academic Achievements & Results', 'count' => '42 Posts', 'icon' => '📜', 'url' => '/category/academics'],
                ['name' => 'Sports & Athletics Events', 'count' => '28 Posts', 'icon' => '⚽', 'url' => '/category/sports'],
                ['name' => 'Co-Curricular Workshops & MUN', 'count' => '19 Posts', 'icon' => '🎭', 'url' => '/category/events'],
                ['name' => 'Admissions & Circular Notices', 'count' => '35 Posts', 'icon' => '📢', 'url' => '/category/notices'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $cats = (array) $this->setting($settings, 'cats', []);
        $cardsHtml = '';

        foreach ($cats as $c) {
            $name  = $this->e($c['name'] ?? '');
            $count = $this->e($c['count'] ?? '0');
            $icon  = $this->e($c['icon'] ?? '📰');
            $url   = $this->e($c['url'] ?? '#');

            $cardsHtml .= <<<HTML
            <a href="{$url}" class="pro-clist-card">
                <span class="pro-clist-icon">{$icon}</span>
                <div class="pro-clist-info">
                    <div class="pro-clist-name">{$name}</div>
                    <div class="pro-clist-count">{$count}</div>
                </div>
                <span class="pro-clist-arrow">→</span>
            </a>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-clist-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; max-width: 1000px; margin: 30px auto 0; padding: 0 16px; }
        .pro-clist-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 20px; text-decoration: none; display: flex; align-items: center; gap: 14px; box-shadow: 0 6px 20px rgba(11,37,69,.05); transition: all .3s ease; }
        .pro-clist-card:hover { border-color: #c79a3b; transform: translateY(-4px); box-shadow: 0 12px 28px rgba(11,37,69,.12); background: #fdf6e2; }
        .pro-clist-icon { font-size: 28px; }
        .pro-clist-info { flex: 1; }
        .pro-clist-name { font-size: 15px; font-weight: 700; color: #0b2545; }
        .pro-clist-count { font-size: 12px; color: #64748b; margin-top: 2px; }
        .pro-clist-arrow { font-size: 16px; font-weight: 800; color: #c79a3b; transition: transform .2s; }
        .pro-clist-card:hover .pro-clist-arrow { transform: translateX(4px); }
        </style>

        <section class="pro-clist-sec">
            {$head}
            <div class="pro-clist-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
