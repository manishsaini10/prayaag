<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Image Morphing Blob.
 * Organic animated blob shape mask with high-resolution image background.
 */
class ImageMorphingWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-image-morphing';
    }

    public function label(): string
    {
        return 'Image Morphing';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Experiential Learning',
            'heading' => 'Creative & Physical Development',
            'image'   => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80',
            'caption' => 'Hands-on Science & Arts Workshops on Campus',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $img     = $this->e($this->setting($settings, 'image'));
        $caption = $this->e($this->setting($settings, 'caption'));

        return <<<HTML
        <style>
        .ek-im-wrap { display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 30px auto; padding: 0 16px; }
        .ek-im-blob { width: 340px; height: 340px; background-image: url('{$img}'); background-size: cover; background-position: center; border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; animation: ek-morph 8s ease-in-out infinite; box-shadow: 0 16px 40px rgba(11,37,69,.18); border: 4px solid #c79a3b; }
        .ek-im-caption { font-size: 15px; font-weight: 700; color: #0b2545; margin-top: 20px; text-align: center; }
        @keyframes ek-morph {
            0% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
            100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
        }
        </style>

        <section class="ek-im-sec">
            {$head}
            <div class="ek-im-wrap">
                <div class="ek-im-blob"></div>
                <div class="ek-im-caption">{$caption}</div>
            </div>
        </section>
        HTML;
    }
}
