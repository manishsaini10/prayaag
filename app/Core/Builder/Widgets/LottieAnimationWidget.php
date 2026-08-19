<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Lottie Animated Graphic.
 * Vector animated hero illustration container with glowing gold accent ring.
 */
class LottieAnimationWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-lottie';
    }

    public function label(): string
    {
        return 'Lottie Animated Graphic';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Interactive Learning',
            'heading' => 'Smart Digital Education Engine',
            'caption' => 'Real-time interactive STEM modules & virtual science labs',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $caption = $this->e($this->setting($settings, 'caption'));

        return <<<HTML
        <style>
        .pro-lott-wrap { display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 30px auto; padding: 0 16px; }
        .pro-lott-box { width: 300px; height: 300px; background: linear-gradient(135deg, #0b2545, #1c3a6e); border: 3px solid #c79a3b; border-radius: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 16px 40px rgba(11,37,69,.2); position: relative; overflow: hidden; }
        .pro-lott-box::before { content: ''; position: absolute; inset: -50%; background: conic-gradient(from 0deg, transparent, rgba(199,154,59,.4), transparent); animation: pro-lott-spin 6s linear infinite; }
        .pro-lott-inner { position: relative; z-index: 2; font-size: 80px; animation: pro-lott-bounce 2.5s ease-in-out infinite; }
        .pro-lott-caption { font-size: 15px; font-weight: 700; color: #0b2545; margin-top: 20px; text-align: center; }
        @keyframes pro-lott-spin { 100% { transform: rotate(360deg); } }
        @keyframes pro-lott-bounce { 0%, 100% { transform: translateY(0) scale(1); } 50% { transform: translateY(-12px) scale(1.05); } }
        </style>

        <section class="pro-lott-sec">
            {$head}
            <div class="pro-lott-wrap">
                <div class="pro-lott-box">
                    <div class="pro-lott-inner">🚀</div>
                </div>
                <div class="pro-lott-caption">{$caption}</div>
            </div>
        </section>
        HTML;
    }
}
