<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — 3D Flip Box Card Grid.
 * Interactive cards that flip 180 degrees on hover revealing back content.
 */
class FlipBoxWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-flipbox';
    }

    public function label(): string
    {
        return '3D Flip Box';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Interactive Features',
            'heading' => 'Core Values & Pillars',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Hover over each card to explore details.',
            'boxes'   => [
                [
                    'icon'        => '🎓',
                    'front_title' => 'Academic Rigor',
                    'front_sub'   => 'Hover to reveal details',
                    'back_title'  => 'Excellence in Pedagogy',
                    'back_desc'   => 'Lorem ipsum dolor sit amet, offering CBSE curriculum integrated with competitive exam foundation programs.',
                    'btn_text'    => 'Explore Academics →',
                    'btn_url'     => '/academics',
                ],
                [
                    'icon'        => '🔬',
                    'front_title' => 'Innovation & STEM',
                    'front_sub'   => 'Hover to reveal details',
                    'back_title'  => 'State-of-the-Art Labs',
                    'back_desc'   => 'Hands-on experiential learning in Robotics, Physics, Chemistry, Biology and Computer Science labs.',
                    'btn_text'    => 'View Campus Labs →',
                    'btn_url'     => '/facilities',
                ],
                [
                    'icon'        => '⚽',
                    'front_title' => 'Sports & Athletics',
                    'front_sub'   => 'Hover to reveal details',
                    'back_title'  => 'Physical & Mental Fitness',
                    'back_desc'   => 'Professional coaching for Swimming, Karate, Lawn Tennis, Football and Basketball championships.',
                    'btn_text'    => 'Sports Facilities →',
                    'btn_url'     => '/facilities',
                ],
                [
                    'icon'        => '🌟',
                    'front_title' => 'Character Building',
                    'front_sub'   => 'Hover to reveal details',
                    'back_title'  => 'Values & Leadership',
                    'back_desc'   => 'Instilling discipline, empathy, global citizenship and ethical leadership in every student.',
                    'btn_text'    => 'Our Vision →',
                    'btn_url'     => '/about-us',
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $cardsHtml = '';
        $boxes = (array) $this->setting($settings, 'boxes', []);

        foreach ($boxes as $b) {
            $icon       = $this->e($b['icon'] ?? '🌟');
            $fTitle     = $this->e($b['front_title'] ?? 'Front Title');
            $fSub       = $this->e($b['front_sub'] ?? '');
            $bTitle     = $this->e($b['back_title'] ?? 'Back Title');
            $bDesc      = $this->e($b['back_desc'] ?? '');
            $btnText    = $this->e($b['btn_text'] ?? 'Learn More');
            $btnUrl     = $this->e($b['btn_url'] ?? '#');

            $cardsHtml .= <<<HTML
            <div class="ek-fb-card">
                <div class="ek-fb-inner">
                    <!-- Front Face -->
                    <div class="ek-fb-face ek-fb-front">
                        <div class="ek-fb-icon">{$icon}</div>
                        <h3 class="ek-fb-title">{$fTitle}</h3>
                        <p class="ek-fb-sub">{$fSub}</p>
                        <div class="ek-fb-flip-indicator">↻ Hover</div>
                    </div>
                    <!-- Back Face -->
                    <div class="ek-fb-face ek-fb-back">
                        <h3 class="ek-fb-title">{$bTitle}</h3>
                        <p class="ek-fb-desc">{$bDesc}</p>
                        <a href="{$btnUrl}" class="ek-fb-btn">{$btnText}</a>
                    </div>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-fb-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; max-width: 1140px; margin: 30px auto 0; padding: 0 16px; }
        .ek-fb-card { height: 320px; perspective: 1000px; cursor: pointer; }
        .ek-fb-inner { position: relative; width: 100%; height: 100%; text-align: center; transition: transform .6s cubic-bezier(.4,.2,.2,1); transform-style: preserve-3d; border-radius: 18px; box-shadow: 0 10px 30px rgba(11,37,69,.08); }
        .ek-fb-card:hover .ek-fb-inner { transform: rotateY(180deg); }
        .ek-fb-face { position: absolute; inset: 0; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; border-radius: 18px; padding: 28px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1.5px solid #e2e8f0; }
        .ek-fb-front { background: #ffffff; color: #0b2545; }
        .ek-fb-back { background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #ffffff; transform: rotateY(180deg); border-color: #0b2545; }
        .ek-fb-icon { font-size: 48px; margin-bottom: 16px; }
        .ek-fb-front .ek-fb-title { font-size: 20px; font-weight: 700; color: #0b2545; margin: 0 0 8px; }
        .ek-fb-sub { font-size: 13px; color: #64748b; margin: 0; }
        .ek-fb-flip-indicator { position: absolute; bottom: 16px; font-size: 11px; font-weight: 700; color: #c79a3b; text-transform: uppercase; letter-spacing: .8px; background: #fdf6e2; padding: 3px 10px; border-radius: 999px; }
        .ek-fb-back .ek-fb-title { font-size: 18px; font-weight: 700; color: #c79a3b; margin: 0 0 12px; }
        .ek-fb-desc { font-size: 13.5px; color: #cbd5e1; margin: 0 0 20px; line-height: 1.55; }
        .ek-fb-btn { display: inline-block; background: linear-gradient(135deg, #c79a3b, #e0b94e); color: #0b2545; font-size: 13px; font-weight: 700; padding: 10px 20px; border-radius: 999px; text-decoration: none; box-shadow: 0 4px 14px rgba(199,154,59,.3); transition: all .2s; }
        .ek-fb-btn:hover { background: #ffffff; color: #0b2545; }
        </style>

        <section class="ek-fb-sec">
            {$head}
            <div class="ek-fb-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
