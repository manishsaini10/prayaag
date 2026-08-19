<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Header Offcanvas Drawer.
 * Side sliding drawer trigger & navigation panel overlay.
 */
class HeaderOffcanvasWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-header-offcanvas';
    }

    public function label(): string
    {
        return 'Header Offcanvas Drawer';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'btn_text' => '☰ Menu & Quick Links',
            'title'    => 'Prayaag International School',
            'desc'     => 'Empowering future leaders through holistic learning and academic excellence.',
            'links'    => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Admissions 2026-27', 'url' => '/admissions'],
                ['title' => 'Academic Curriculum', 'url' => '/academics'],
                ['title' => 'Campus Facilities', 'url' => '/facilities'],
                ['title' => 'Contact Us', 'url' => '/contact'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $btnText = $this->e($this->setting($settings, 'btn_text'));
        $title   = $this->e($this->setting($settings, 'title'));
        $desc    = $this->e($this->setting($settings, 'desc'));
        $links   = (array) $this->setting($settings, 'links', []);

        $ocId = 'pro-oc-' . uniqid();

        $linksHtml = '';
        foreach ($links as $l) {
            $t = $this->e($l['title'] ?? '');
            $u = $this->e($l['url'] ?? '#');
            $linksHtml .= <<<HTML
            <li><a href="{$u}" class="pro-oc-link">{$t}</a></li>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-oc-wrap { text-align: center; margin: 20px 0; }
        .pro-oc-btn { background: #0b2545; color: #c79a3b; border: 1.5px solid #c79a3b; font-size: 14px; font-weight: 700; padding: 10px 22px; border-radius: 999px; cursor: pointer; transition: all .2s; font-family: inherit; }
        .pro-oc-btn:hover { background: #c79a3b; color: #0b2545; }
        .pro-oc-panel { position: fixed; top: 0; right: 0; width: 320px; height: 100vh; background: #0b2545; color: #ffffff; z-index: 9999; padding: 32px 24px; box-shadow: -10px 0 30px rgba(0,0,0,.4); transform: translateX(100%); transition: transform .4s ease; overflow-y: auto; text-align: left; }
        .pro-oc-panel.active { transform: translateX(0); }
        .pro-oc-close { position: absolute; top: 20px; right: 20px; background: none; border: none; color: #ffffff; font-size: 24px; cursor: pointer; }
        .pro-oc-title { font-size: 20px; font-weight: 800; color: #c79a3b; margin: 20px 0 8px; }
        .pro-oc-desc { font-size: 13px; color: rgba(255,255,255,.75); margin: 0 0 24px; line-height: 1.5; }
        .pro-oc-nav { list-style: none; padding: 0; margin: 0 0 30px; }
        .pro-oc-nav li { margin-bottom: 12px; }
        .pro-oc-link { color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; transition: color .2s; }
        .pro-oc-link:hover { color: #c79a3b; }
        </style>

        <div class="pro-oc-wrap">
            <button type="button" class="pro-oc-btn" onclick="document.getElementById('{$ocId}').classList.add('active')">{$btnText}</button>
        </div>

        <div class="pro-oc-panel" id="{$ocId}">
            <button type="button" class="pro-oc-close" onclick="document.getElementById('{$ocId}').classList.remove('active')">✕</button>
            <h3 class="pro-oc-title">{$title}</h3>
            <p class="pro-oc-desc">{$desc}</p>
            <ul class="pro-oc-nav">
                {$linksHtml}
            </ul>
        </div>
        HTML;
    }
}
