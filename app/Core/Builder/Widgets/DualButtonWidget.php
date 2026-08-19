<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Dual Button / Callout Banner.
 * Features two side-by-side call-to-action buttons with divider and badge.
 */
class DualButtonWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-dual-button';
    }

    public function label(): string
    {
        return 'Dual Button CTA';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow'          => '★ Take the Next Step',
            'heading'          => 'Ready to Join Prayaag International School?',
            'sub'              => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Apply online or schedule a personal campus tour.',
            'primary_label'    => 'Online Admission Form →',
            'primary_url'      => '/admissions',
            'secondary_label'  => 'Schedule Campus Visit 📍',
            'secondary_url'    => '/contact',
            'divider_text'     => 'OR',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $eyebrow    = $this->e($this->setting($settings, 'eyebrow'));
        $heading    = $this->e($this->setting($settings, 'heading'));
        $sub        = $this->e($this->setting($settings, 'sub'));
        $pLabel     = $this->e($this->setting($settings, 'primary_label'));
        $pUrl       = $this->e($this->setting($settings, 'primary_url', '#'));
        $sLabel     = $this->e($this->setting($settings, 'secondary_label'));
        $sUrl       = $this->e($this->setting($settings, 'secondary_url', '#'));
        $divider    = $this->e($this->setting($settings, 'divider_text', 'OR'));

        return <<<HTML
        <style>
        .ek-db-sec { background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 48px 24px; text-align: center; max-width: 1000px; margin: 30px auto; position: relative; box-shadow: 0 12px 32px rgba(11,37,69,.06); }
        .ek-db-badge { display: inline-block; background: #fdf6e2; color: #c79a3b; border: 1px solid rgba(199,154,59,.3); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 5px 16px; border-radius: 999px; margin-bottom: 14px; }
        .ek-db-title { font-size: 28px; font-weight: 800; color: #0b2545; margin: 0 0 10px; line-height: 1.25; }
        .ek-db-sub { font-size: 15px; color: #64748b; margin: 0 0 32px; max-width: 600px; margin-inline: auto; }
        .ek-db-group { display: flex; align-items: center; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .ek-btn-primary { background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #ffffff; font-size: 15px; font-weight: 700; padding: 14px 30px; border-radius: 999px; text-decoration: none; box-shadow: 0 8px 24px rgba(11,37,69,.25); transition: all .2s; }
        .ek-btn-primary:hover { background: #c79a3b; color: #0b2545; transform: translateY(-2px); box-shadow: 0 12px 28px rgba(199,154,59,.4); }
        .ek-db-divider { background: #e2e8f0; color: #64748b; font-size: 11px; font-weight: 800; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; }
        .ek-btn-secondary { background: #ffffff; color: #0b2545; border: 2px solid #0b2545; font-size: 15px; font-weight: 700; padding: 12px 28px; border-radius: 999px; text-decoration: none; transition: all .2s; }
        .ek-btn-secondary:hover { background: #0b2545; color: #ffffff; transform: translateY(-2px); }
        @media(max-width: 600px) { .ek-db-group { flex-direction: column; gap: 12px; } .ek-btn-primary, .ek-btn-secondary { width: 100%; text-align: center; } }
        </style>

        <section class="ek-db-sec">
            <span class="ek-db-badge">{$eyebrow}</span>
            <h2 class="ek-db-title">{$heading}</h2>
            <p class="ek-db-sub">{$sub}</p>
            <div class="ek-db-group">
                <a href="{$pUrl}" class="ek-btn-primary">{$pLabel}</a>
                <span class="ek-db-divider">{$divider}</span>
                <a href="{$sUrl}" class="ek-btn-secondary">{$sLabel}</a>
            </div>
        </section>
        HTML;
    }
}
