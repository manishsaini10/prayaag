<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Motion Text / Kinetic Ticker.
 * Infinite horizontal scrolling marquee text banner.
 */
class MotionTextWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-motion-text';
    }

    public function label(): string
    {
        return 'Motion Text / Marquee';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'text'  => 'ADMISSIONS OPEN FOR 2026-27 ★ 100% BOARD RESULTS ★ INTEGRATED JEE / NEET COACHING ★ OLYMPIAD WINNERS ★ WORLD-CLASS CAMPUS',
            'speed' => '25s',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $text  = $this->e($this->setting($settings, 'text'));
        $speed = $this->e($this->setting($settings, 'speed', '25s'));

        return <<<HTML
        <style>
        .ek-mt-wrap { background: linear-gradient(90deg, #0b2545, #1c3a6e, #0b2545); color: #c79a3b; padding: 18px 0; overflow: hidden; white-space: nowrap; user-select: none; border-y: 1px solid rgba(199,154,59,.3); margin: 30px 0; }
        .ek-mt-track { display: inline-block; animation: ek-scroll {$speed} linear infinite; font-size: 18px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; }
        @keyframes ek-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        </style>

        <div class="ek-mt-wrap">
            <div class="ek-mt-track">
                {$text} &nbsp;✦&nbsp; {$text} &nbsp;✦&nbsp;
            </div>
        </div>
        HTML;
    }
}
