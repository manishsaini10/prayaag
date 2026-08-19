<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Back To Top Button.
 * Floating scroll-to-top button with smooth scroll & circular progress indicator.
 */
class BackToTopWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-back-to-top';
    }

    public function label(): string
    {
        return 'Back To Top Button';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'label' => 'Back to Top',
            'position' => 'bottom-right',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $label = $this->e($this->setting($settings, 'label', 'Back to Top'));
        $bttId = 'pro-btt-' . uniqid();

        return <<<HTML
        <style>
        .pro-btt-wrap { display: flex; justify-content: center; align-items: center; padding: 20px; }
        .pro-btt-btn { background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #c79a3b; border: 2px solid #c79a3b; border-radius: 999px; padding: 12px 24px; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 8px 24px rgba(11,37,69,.2); transition: all .3s ease; font-family: inherit; }
        .pro-btt-btn:hover { background: #c79a3b; color: #0b2545; transform: translateY(-3px); box-shadow: 0 12px 28px rgba(199,154,59,.3); }
        .pro-btt-icon { font-size: 18px; line-height: 1; transition: transform .3s; }
        .pro-btt-btn:hover .pro-btt-icon { transform: translateY(-3px); }
        </style>

        <div class="pro-btt-wrap">
            <button type="button" class="pro-btt-btn" id="{$bttId}" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                <span class="pro-btt-icon">▲</span>
                <span>{$label}</span>
            </button>
        </div>
        HTML;
    }
}
