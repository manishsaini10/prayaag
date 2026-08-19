<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Image Box Grid.
 * Photo cards with top image zoom, title, description, and action link.
 */
class ImageBoxWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-image-box';
    }

    public function label(): string
    {
        return 'Image Box Grid';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Beyond Academics',
            'heading' => 'Holistic Co-Curricular Programs',
            'boxes'   => [
                ['title' => 'Performing Arts & Theatre', 'desc' => 'Drama, classical music, and modern dance workshops.', 'image' => 'https://images.unsplash.com/photo-1460723237483-7a6dc9d0b212?w=600&auto=format&fit=crop&q=80', 'url' => '/arts'],
                ['title' => 'Equestrian & Horse Riding', 'desc' => 'Professional equestrian training and obstacle jumping.', 'image' => 'https://images.unsplash.com/photo-1553284965-83fd3e82fa5a?w=600&auto=format&fit=crop&q=80', 'url' => '/sports'],
                ['title' => 'Astronomy & Stargazing Club', 'desc' => 'High-power telescopes and space research projects.', 'image' => 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?w=600&auto=format&fit=crop&q=80', 'url' => '/clubs'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $boxes = (array) $this->setting($settings, 'boxes', []);
        $cardsHtml = '';

        foreach ($boxes as $b) {
            $title = $this->e($b['title'] ?? '');
            $desc  = $this->e($b['desc'] ?? '');
            $img   = $this->e($b['image'] ?? '');
            $url   = $this->e($b['url'] ?? '#');

            $cardsHtml .= <<<HTML
            <div class="pro-imgb-card">
                <div class="pro-imgb-thumb">
                    <img src="{$img}" alt="{$title}" class="pro-imgb-img">
                </div>
                <div class="pro-imgb-body">
                    <h3 class="pro-imgb-title">{$title}</h3>
                    <p class="pro-imgb-desc">{$desc}</p>
                    <a href="{$url}" class="pro-imgb-link">Learn More →</a>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-imgb-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; max-width: 1100px; margin: 30px auto 0; padding: 0 16px; }
        .pro-imgb-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; overflow: hidden; box-shadow: 0 8px 24px rgba(11,37,69,.06); transition: all .3s ease; display: flex; flex-direction: column; }
        .pro-imgb-card:hover { border-color: #c79a3b; transform: translateY(-6px); box-shadow: 0 16px 36px rgba(11,37,69,.12); }
        .pro-imgb-thumb { overflow: hidden; height: 180px; }
        .pro-imgb-img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
        .pro-imgb-card:hover .pro-imgb-img { transform: scale(1.08); }
        .pro-imgb-body { padding: 22px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .pro-imgb-title { font-size: 18px; font-weight: 800; color: #0b2545; margin: 0 0 8px; }
        .pro-imgb-desc { font-size: 14px; color: #64748b; margin: 0 0 16px; line-height: 1.6; flex: 1; }
        .pro-imgb-link { font-size: 13px; font-weight: 700; color: #c79a3b; text-decoration: none; transition: color .2s; }
        .pro-imgb-link:hover { color: #0b2545; }
        </style>

        <section class="pro-imgb-sec">
            {$head}
            <div class="pro-imgb-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
