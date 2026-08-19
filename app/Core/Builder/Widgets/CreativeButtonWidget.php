<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Creative Interactive Button.
 * Multi-effect button styling with gradient glow, liquid border, and hover motion.
 */
class CreativeButtonWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-creative-button';
    }

    public function label(): string
    {
        return 'Creative Button';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'text'     => 'Explore School Prospectus 2026-27 →',
            'url'      => '/downloads',
            'subtext'  => 'Instant PDF Download (4.2 MB)',
            'style'    => 'glow',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $text    = $this->e($this->setting($settings, 'text'));
        $url     = $this->e($this->setting($settings, 'url', '#'));
        $subtext = $this->e($this->setting($settings, 'subtext'));

        return <<<HTML
        <style>
        .ek-cb-wrap { text-align: center; margin: 30px auto; padding: 0 16px; }
        .ek-cb-btn { position: relative; display: inline-flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px 36px; background: linear-gradient(135deg, #0b2545 0%, #1c3a6e 60%, #c79a3b 100%); color: #ffffff; text-decoration: none; border-radius: 999px; font-weight: 700; font-size: 16px; box-shadow: 0 10px 30px rgba(11,37,69,.25); transition: all .3s cubic-bezier(.2,.7,.2,1); overflow: hidden; }
        .ek-cb-btn::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,.3) 0%, transparent 60%); transform: scale(0); transition: transform .5s ease; pointer-events: none; }
        .ek-cb-btn:hover::before { transform: scale(1); }
        .ek-cb-btn:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 16px 40px rgba(199,154,59,.4); color: #ffffff; }
        .ek-cb-sub { font-size: 11px; opacity: .8; font-weight: 500; margin-top: 3px; letter-spacing: .5px; }
        </style>

        <div class="ek-cb-wrap">
            <a href="{$url}" class="ek-cb-btn">
                <span>{$text}</span>
                <span class="ek-cb-sub">{$subtext}</span>
            </a>
        </div>
        HTML;
    }
}
