<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Advanced Content Slider / Banner Carousel.
 * Multi-slide carousel with interactive pagination and custom slide buttons.
 */
class AdvancedSliderWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-advanced-slider';
    }

    public function label(): string
    {
        return 'Advanced Slider';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'slides' => [
                [
                    'kicker'   => '★ CBSE Affiliated Senior Secondary School',
                    'title'    => 'Nurturing Minds, Building Character & Global Leaders',
                    'sub'      => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. World-class campus & faculty.',
                    'image'    => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&auto=format&fit=crop&q=80',
                    'btn_text' => 'Explore Campus Tour →',
                    'btn_url'  => '/facilities',
                ],
                [
                    'kicker'   => '★ Admissions Open 2026-27',
                    'title'    => 'State-of-the-Art STEM & Robotics Learning Center',
                    'sub'      => 'Hands-on experiential learning for modern technology & scientific discovery.',
                    'image'    => 'https://images.unsplash.com/photo-1562774053-701939374585?w=1200&auto=format&fit=crop&q=80',
                    'btn_text' => 'Apply Online Now →',
                    'btn_url'  => '/admissions',
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $slides = (array) $this->setting($settings, 'slides', []);
        $sliderId = 'ek-as-' . uniqid();

        $slidesHtml = '';
        $dotsHtml   = '';

        foreach ($slides as $idx => $s) {
            $kicker = $this->e($s['kicker'] ?? '');
            $title  = $this->e($s['title'] ?? '');
            $sub    = $this->e($s['sub'] ?? '');
            $img    = $this->e($s['image'] ?? '');
            $btn    = $this->e($s['btn_text'] ?? 'Learn More');
            $url    = $this->e($s['btn_url'] ?? '#');
            $active = $idx === 0 ? 'active' : '';

            $slidesHtml .= <<<HTML
            <div class="ek-as-slide {$active}" style="background-image: linear-gradient(rgba(11,37,69,.75), rgba(11,37,69,.75)), url('{$img}');">
                <div class="ek-as-content">
                    <span class="ek-as-kicker">{$kicker}</span>
                    <h2 class="ek-as-title">{$title}</h2>
                    <p class="ek-as-sub">{$sub}</p>
                    <a href="{$url}" class="ek-as-btn">{$btn}</a>
                </div>
            </div>
            HTML;

            $dotsHtml .= '<button type="button" class="ek-as-dot ' . $active . '" onclick="switchEkSlide(\'' . $sliderId . '\', ' . $idx . ')"></button>';
        }

        return <<<HTML
        <style>
        .ek-as-wrap { position: relative; width: 100%; height: 480px; border-radius: 20px; overflow: hidden; box-shadow: 0 16px 40px rgba(11,37,69,.15); max-width: 1140px; margin: 30px auto; }
        .ek-as-slide { position: absolute; inset: 0; background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; padding: 40px 24px; text-align: center; color: #ffffff; opacity: 0; transition: opacity .6s ease; pointer-events: none; }
        .ek-as-slide.active { opacity: 1; pointer-events: auto; }
        .ek-as-content { max-width: 750px; }
        .ek-as-kicker { display: inline-block; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #c79a3b; background: rgba(199,154,59,.15); border: 1px solid rgba(199,154,59,.3); padding: 6px 16px; border-radius: 999px; margin-bottom: 16px; }
        .ek-as-title { font-size: 34px; font-weight: 800; color: #ffffff; margin: 0 0 16px; line-height: 1.25; }
        .ek-as-sub { font-size: 16px; color: #cbd5e1; margin: 0 0 28px; line-height: 1.6; }
        .ek-as-btn { display: inline-block; background: linear-gradient(135deg, #c79a3b, #e0b94e); color: #0b2545; font-size: 15px; font-weight: 700; padding: 12px 30px; border-radius: 999px; text-decoration: none; box-shadow: 0 8px 24px rgba(199,154,59,.35); transition: transform .2s; }
        .ek-as-btn:hover { transform: translateY(-2px); }
        .ek-as-dots { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10; }
        .ek-as-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,.4); border: none; cursor: pointer; transition: all .3s; }
        .ek-as-dot.active { background: #c79a3b; width: 28px; border-radius: 999px; }
        @media(max-width: 768px) { .ek-as-wrap { height: 380px; } .ek-as-title { font-size: 24px; } }
        </style>

        <div class="ek-as-wrap" id="{$sliderId}">
            {$slidesHtml}
            <div class="ek-as-dots">
                {$dotsHtml}
            </div>
        </div>

        <script>
        function switchEkSlide(sliderId, targetIdx) {
            var wrap = document.getElementById(sliderId);
            if (!wrap) return;
            var slides = wrap.querySelectorAll('.ek-as-slide');
            var dots   = wrap.querySelectorAll('.ek-as-dot');
            slides.forEach(function(s, idx) { s.classList.toggle('active', idx === targetIdx); });
            dots.forEach(function(d, idx) { d.classList.toggle('active', idx === targetIdx); });
        }
        </script>
        HTML;
    }
}
