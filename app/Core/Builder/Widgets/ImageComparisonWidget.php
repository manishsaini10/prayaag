<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Image Comparison (Before / After Slider).
 * Interactive slider comparing two images side by side.
 */
class ImageComparisonWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-image-comparison';
    }

    public function label(): string
    {
        return 'Image Comparison';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow'      => 'Campus Evolution',
            'heading'      => 'Then vs Now — Our Growth Story',
            'sub'          => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Drag the handle to see our campus transformation.',
            'before_image' => 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?w=1000&auto=format&fit=crop&q=80',
            'before_label' => 'Initial Campus (2016)',
            'after_image'  => 'https://images.unsplash.com/photo-1562774053-701939374585?w=1000&auto=format&fit=crop&q=80',
            'after_label'  => 'Modern Campus (2026)',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $bImg   = $this->e($this->setting($settings, 'before_image'));
        $bLbl   = $this->e($this->setting($settings, 'before_label', 'Before'));
        $aImg   = $this->e($this->setting($settings, 'after_image'));
        $aLbl   = $this->e($this->setting($settings, 'after_label', 'After'));
        $compClass = 'ek-ic-' . uniqid();

        return <<<HTML
        <style>
        .ek-ic-container { max-width: 1000px; margin: 30px auto 0; padding: 0 16px; }
        .ek-ic-wrap { position: relative; width: 100%; height: 480px; overflow: hidden; border-radius: 18px; border: 2px solid #e2e8f0; box-shadow: 0 16px 40px rgba(11,37,69,.12); user-select: none; }
        .ek-ic-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .ek-ic-after-wrap { position: absolute; inset: 0; width: 50%; overflow: hidden; border-right: 3px solid #c79a3b; }
        .ek-ic-after-wrap .ek-ic-img { width: 1000px; max-width: none; }
        .ek-ic-badge { position: absolute; top: 16px; background: rgba(11,37,69,.85); color: #ffffff; backdrop-filter: blur(4px); font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 8px; letter-spacing: .5px; }
        .ek-ic-before-badge { right: 16px; }
        .ek-ic-after-badge { left: 16px; background: rgba(199,154,59,.9); color: #0b2545; }
        .ek-ic-slider { position: absolute; top: 0; bottom: 0; width: 100%; opacity: 0; cursor: ew-resize; z-index: 10; margin: 0; }
        .ek-ic-handle { position: absolute; top: 50%; left: 50%; width: 44px; height: 44px; background: #c79a3b; color: #0b2545; border: 3px solid #ffffff; border-radius: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.3); pointer-events: none; z-index: 5; }
        @media(max-width: 768px) { .ek-ic-wrap { height: 320px; } }
        </style>

        <section class="ek-ic-sec">
            {$head}
            <div class="ek-ic-container">
                <div class="ek-ic-wrap" id="{$compClass}">
                    <!-- Before Image (Full) -->
                    <img src="{$bImg}" alt="{$bLbl}" class="ek-ic-img">
                    <span class="ek-ic-badge ek-ic-before-badge">{$bLbl}</span>

                    <!-- After Image (Clipped) -->
                    <div class="ek-ic-after-wrap" id="{$compClass}-clip">
                        <img src="{$aImg}" alt="{$aLbl}" class="ek-ic-img" id="{$compClass}-after-img">
                        <span class="ek-ic-badge ek-ic-after-badge">{$aLbl}</span>
                    </div>

                    <!-- Range Input -->
                    <input type="range" min="0" max="100" value="50" class="ek-ic-slider" oninput="
                        var val = this.value + '%';
                        document.getElementById('{$compClass}-clip').style.width = val;
                        document.getElementById('{$compClass}-handle').style.left = val;
                    ">
                    <div class="ek-ic-handle" id="{$compClass}-handle">↔</div>
                </div>
            </div>
        </section>
        <script>
        (function(){
            function resize() {
                var wrap = document.getElementById('{$compClass}');
                var img = document.getElementById('{$compClass}-after-img');
                if (wrap && img) img.style.width = wrap.offsetWidth + 'px';
            }
            window.addEventListener('resize', resize);
            setTimeout(resize, 200);
        })();
        </script>
        HTML;
    }
}
