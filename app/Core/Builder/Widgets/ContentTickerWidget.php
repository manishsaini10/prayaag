<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Content Ticker / Breaking News Bar.
 * Auto-rotating news ticker bar with category badge and timestamp.
 */
class ContentTickerWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-content-ticker';
    }

    public function label(): string
    {
        return 'Content Ticker';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'label' => '⚡ BREAKING NEWS',
            'items' => [
                ['title' => 'CBSE Class X & XII Board Exam Schedule 2026 Released — Check Notice Board', 'url' => '/notice-board'],
                ['title' => 'Admissions Open for Session 2026-27 (Nursery to Class XI) — Apply Online', 'url' => '/admissions'],
                ['title' => 'Prayaag Wins National Karate & Shooting Championship Gold Medal', 'url' => '/achievements'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $badgeLabel = $this->e($this->setting($settings, 'label', '⚡ BREAKING NEWS'));
        $items      = (array) $this->setting($settings, 'items', []);
        $tickerId   = 'ek-ct-' . uniqid();

        $itemsHtml = '';
        foreach ($items as $idx => $it) {
            $title  = $this->e($it['title'] ?? '');
            $url    = $this->e($it['url'] ?? '#');
            $active = $idx === 0 ? 'active' : '';

            $itemsHtml .= <<<HTML
            <a href="{$url}" class="ek-ct-item {$active}">{$title}</a>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-ct-bar { background: #0b2545; color: #ffffff; border-radius: 12px; padding: 10px 16px; display: flex; align-items: center; gap: 14px; max-width: 1000px; margin: 20px auto; overflow: hidden; box-shadow: 0 4px 16px rgba(11,37,69,.12); }
        .ek-ct-badge { background: #c79a3b; color: #0b2545; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; }
        .ek-ct-viewport { flex: 1; height: 24px; position: relative; overflow: hidden; }
        .ek-ct-item { position: absolute; inset: 0; color: #f8fafc; font-size: 13.5px; font-weight: 600; text-decoration: none; display: flex; align-items: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; opacity: 0; transform: translateY(100%); transition: all .4s ease; }
        .ek-ct-item.active { opacity: 1; transform: translateY(0); }
        .ek-ct-item:hover { color: #c79a3b; }
        </style>

        <div class="ek-ct-bar">
            <span class="ek-ct-badge">{$badgeLabel}</span>
            <div class="ek-ct-viewport" id="{$tickerId}">
                {$itemsHtml}
            </div>
        </div>

        <script>
        (function() {
            var wrap = document.getElementById('{$tickerId}');
            if (!wrap) return;
            var items = wrap.querySelectorAll('.ek-ct-item');
            if (items.length <= 1) return;
            var curr = 0;
            setInterval(function() {
                items[curr].classList.remove('active');
                curr = (curr + 1) % items.length;
                items[curr].classList.add('active');
            }, 3500);
        })();
        </script>
        HTML;
    }
}
