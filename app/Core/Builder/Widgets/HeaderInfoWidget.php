<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Header Contact Info Bar.
 * Top bar header displaying contact numbers, email, CBSE affiliation & quick links.
 */
class HeaderInfoWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-header-info';
    }

    public function label(): string
    {
        return 'Header Contact Info Bar';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'phone'    => '+91 98765 43210',
            'email'    => 'info@prayaagschool.com',
            'aff'      => 'CBSE Affiliation No: 2132890',
            'cta_text' => 'Online Admission 2026-27 →',
            'cta_url'  => '/admissions',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $phone   = $this->e($this->setting($settings, 'phone'));
        $email   = $this->e($this->setting($settings, 'email'));
        $aff     = $this->e($this->setting($settings, 'aff'));
        $ctaText = $this->e($this->setting($settings, 'cta_text'));
        $ctaUrl  = $this->e($this->setting($settings, 'cta_url', '#'));

        return <<<HTML
        <style>
        .pro-hi-bar { background: #0b2545; color: #ffffff; padding: 10px 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid rgba(199,154,59,.3); }
        .pro-hi-items { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .pro-hi-item { display: inline-flex; align-items: center; gap: 6px; color: rgba(255,255,255,.9); text-decoration: none; }
        .pro-hi-item:hover { color: #c79a3b; }
        .pro-hi-badge { background: rgba(199,154,59,.2); color: #c79a3b; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; border: 1px solid rgba(199,154,59,.4); }
        .pro-hi-cta { background: #c79a3b; color: #0b2545; font-weight: 800; font-size: 12px; padding: 5px 14px; border-radius: 999px; text-decoration: none; transition: transform .2s; }
        .pro-hi-cta:hover { transform: translateY(-1px); background: #e0b94e; }
        @media(max-width: 768px) { .pro-hi-bar { flex-direction: column; text-align: center; } }
        </style>

        <div class="pro-hi-bar">
            <div class="pro-hi-items">
                <a href="tel:{$phone}" class="pro-hi-item">📞 {$phone}</a>
                <a href="mailto:{$email}" class="pro-hi-item">✉️ {$email}</a>
                <span class="pro-hi-badge">📜 {$aff}</span>
            </div>
            <div>
                <a href="{$ctaUrl}" class="pro-hi-cta">{$ctaText}</a>
            </div>
        </div>
        HTML;
    }
}
