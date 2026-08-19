<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Glass Morphism Container.
 * Frosted glass card with glowing backdrop filter and gold accents.
 */
class GlassMorphismWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-glass-morphism';
    }

    public function label(): string
    {
        return 'Glass Morphism Container';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow'  => '★ Premium Campus Experience',
            'heading'  => 'Future-Ready Education in a World-Class Environment',
            'content'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Experience translucent glassmorphism aesthetics integrated with robust academic foundation.',
            'btn_text' => 'Take Virtual Campus Tour →',
            'btn_url'  => '/facilities',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $eyebrow = $this->e($this->setting($settings, 'eyebrow'));
        $heading = $this->e($this->setting($settings, 'heading'));
        $content = $this->e($this->setting($settings, 'content'));
        $btnText = $this->e($this->setting($settings, 'btn_text'));
        $btnUrl  = $this->e($this->setting($settings, 'btn_url', '#'));

        return <<<HTML
        <style>
        .ek-gm-sec { background: linear-gradient(135deg, #0b2545 0%, #1c3a6e 50%, #0b2545 100%); padding: 60px 24px; border-radius: 24px; max-width: 1000px; margin: 30px auto; text-align: center; position: relative; overflow: hidden; }
        .ek-gm-sec::before { content: ''; position: absolute; top: 20%; left: 20%; width: 250px; height: 250px; background: rgba(199,154,59,.35); border-radius: 50%; filter: blur(60px); pointer-events: none; }
        .ek-gm-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1.5px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 40px; color: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,.3); position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }
        .ek-gm-kicker { display: inline-block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #c79a3b; margin-bottom: 12px; }
        .ek-gm-title { font-size: 30px; font-weight: 800; color: #ffffff; margin: 0 0 16px; line-height: 1.3; }
        .ek-gm-desc { font-size: 15px; color: rgba(255,255,255,.85); margin: 0 0 28px; line-height: 1.6; }
        .ek-gm-btn { display: inline-block; background: linear-gradient(135deg, #c79a3b, #e0b94e); color: #0b2545; font-size: 15px; font-weight: 700; padding: 12px 30px; border-radius: 999px; text-decoration: none; box-shadow: 0 8px 24px rgba(199,154,59,.4); transition: transform .2s; }
        .ek-gm-btn:hover { transform: translateY(-2px); }
        </style>

        <section class="ek-gm-sec">
            <div class="ek-gm-card">
                <span class="ek-gm-kicker">{$eyebrow}</span>
                <h2 class="ek-gm-title">{$heading}</h2>
                <p class="ek-gm-desc">{$content}</p>
                <a href="{$btnUrl}" class="ek-gm-btn">{$btnText}</a>
            </div>
        </section>
        HTML;
    }
}
