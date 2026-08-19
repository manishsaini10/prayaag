<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Interactive Image Hotspot.
 * Image with pulsing pin dots revealing popover detail cards on hover.
 */
class ImageHotspotWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-image-hotspot';
    }

    public function label(): string
    {
        return 'Image Hotspot';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Interactive Campus Map',
            'heading' => 'Explore Key Facilities on Campus',
            'image'   => 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?w=1200&auto=format&fit=crop&q=80',
            'hotspots' => [
                ['top' => '25%', 'left' => '30%', 'title' => 'Academic Block A', 'desc' => 'Smart Classrooms for Primary & Middle Wing.'],
                ['top' => '45%', 'left' => '65%', 'title' => 'Olympic Size Swimming Pool', 'desc' => 'Temperature-controlled indoor pool with certified coaches.'],
                ['top' => '70%', 'left' => '40%', 'title' => 'Robotics & AI Hub', 'desc' => 'High-tech 3D printing & STEM innovation lab.'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $img      = $this->e($this->setting($settings, 'image'));
        $hotspots = (array) $this->setting($settings, 'hotspots', []);

        $pinsHtml = '';
        foreach ($hotspots as $hs) {
            $top   = $this->e($hs['top'] ?? '50%');
            $left  = $this->e($hs['left'] ?? '50%');
            $title = $this->e($hs['title'] ?? 'Point');
            $desc  = $this->e($hs['desc'] ?? '');

            $pinsHtml .= <<<HTML
            <div class="ek-hs-pin" style="top: {$top}; left: {$left};">
                <div class="ek-hs-pulse"></div>
                <div class="ek-hs-dot">📍</div>
                <div class="ek-hs-tooltip">
                    <h4 class="ek-hs-title">{$title}</h4>
                    <p class="ek-hs-desc">{$desc}</p>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-hs-container { max-width: 1000px; margin: 30px auto 0; padding: 0 16px; }
        .ek-hs-wrap { position: relative; width: 100%; border-radius: 20px; overflow: hidden; border: 2px solid #e2e8f0; box-shadow: 0 16px 40px rgba(11,37,69,.12); }
        .ek-hs-img { width: 100%; height: auto; display: block; }
        .ek-hs-pin { position: absolute; transform: translate(-50%, -50%); cursor: pointer; z-index: 5; }
        .ek-hs-dot { width: 32px; height: 32px; border-radius: 50%; background: #0b2545; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid #c79a3b; box-shadow: 0 4px 12px rgba(0,0,0,.25); position: relative; z-index: 2; }
        .ek-hs-pulse { position: absolute; inset: -6px; border-radius: 50%; background: rgba(199,154,59,.4); animation: ek-hs-ping 1.8s cubic-bezier(0,0,.2,1) infinite; }
        .ek-hs-tooltip { position: absolute; bottom: 42px; left: 50%; transform: translateX(-50%) translateY(10px); width: 220px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px; box-shadow: 0 10px 28px rgba(11,37,69,.18); opacity: 0; pointer-events: none; transition: all .25s ease; z-index: 10; text-align: left; }
        .ek-hs-pin:hover .ek-hs-tooltip { opacity: 1; pointer-events: auto; transform: translateX(-50%) translateY(0); }
        .ek-hs-title { font-size: 14px; font-weight: 700; color: #0b2545; margin: 0 0 4px; }
        .ek-hs-desc { font-size: 12px; color: #64748b; margin: 0; line-height: 1.4; }
        @keyframes ek-hs-ping { 75%, 100% { transform: scale(1.8); opacity: 0; } }
        </style>

        <section class="ek-hs-sec">
            {$head}
            <div class="ek-hs-container">
                <div class="ek-hs-wrap">
                    <img src="{$img}" alt="Campus Hotspots" class="ek-hs-img">
                    {$pinsHtml}
                </div>
            </div>
        </section>
        HTML;
    }
}
