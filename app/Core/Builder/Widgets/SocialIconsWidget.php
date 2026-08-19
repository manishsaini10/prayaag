<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Social Icons Bar.
 * Animated social media buttons with official brand colors and hover tooltips.
 */
class SocialIconsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-social-icons';
    }

    public function label(): string
    {
        return 'Social Icons Bar';
    }

    public function category(): string
    {
        return 'pro-social';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Stay Connected',
            'heading' => 'Follow Prayaag School on Social Media',
            'icons'   => [
                ['name' => 'Facebook', 'icon' => '📘', 'url' => 'https://facebook.com', 'color' => '#1877f2'],
                ['name' => 'Instagram', 'icon' => '📸', 'url' => 'https://instagram.com', 'color' => '#e4405f'],
                ['name' => 'YouTube', 'icon' => '🎬', 'url' => 'https://youtube.com', 'color' => '#ff0000'],
                ['name' => 'LinkedIn', 'icon' => '💼', 'url' => 'https://linkedin.com', 'color' => '#0a66c2'],
                ['name' => 'X / Twitter', 'icon' => '🐦', 'url' => 'https://x.com', 'color' => '#000000'],
                ['name' => 'WhatsApp', 'icon' => '💬', 'url' => 'https://wa.me/919876543210', 'color' => '#25d366'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $icons = (array) $this->setting($settings, 'icons', []);
        $itemsHtml = '';

        foreach ($icons as $i) {
            $name  = $this->e($i['name'] ?? '');
            $icon  = $this->e($i['icon'] ?? '🔗');
            $url   = $this->e($i['url'] ?? '#');
            $color = $this->e($i['color'] ?? '#0b2545');

            $itemsHtml .= <<<HTML
            <a href="{$url}" target="_blank" class="pro-si-btn" style="--brand: {$color};">
                <span class="pro-si-icon">{$icon}</span>
                <span class="pro-si-name">{$name}</span>
            </a>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-si-wrap { display: flex; flex-wrap: wrap; justify-content: center; gap: 16px; max-width: 900px; margin: 30px auto; padding: 0 16px; }
        .pro-si-btn { display: inline-flex; align-items: center; gap: 10px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 999px; padding: 10px 22px; text-decoration: none; color: #0b2545; font-size: 14px; font-weight: 700; transition: all .3s cubic-bezier(.2,.7,.2,1); box-shadow: 0 4px 16px rgba(11,37,69,.06); }
        .pro-si-btn:hover { background: var(--brand); color: #ffffff; border-color: var(--brand); transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,.18); }
        .pro-si-icon { font-size: 18px; }
        </style>

        <section class="pro-si-sec">
            {$head}
            <div class="pro-si-wrap">
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
