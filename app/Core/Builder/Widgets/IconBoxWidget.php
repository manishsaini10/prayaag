<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Icon Box Card Grid.
 * Interactive feature cards with icons, hover zoom, and gold border accents.
 */
class IconBoxWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-icon-box';
    }

    public function label(): string
    {
        return 'Icon Box Card Grid';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Pillars of Excellence',
            'heading' => 'Why Choose Prayaag International',
            'boxes'   => [
                ['icon' => '🚀', 'title' => 'STEM & Robotics Lab', 'desc' => 'Lorem ipsum dolor sit amet, state-of-the-art robotics & AI training.'],
                ['icon' => '🏆', 'title' => 'Sports Academy', 'desc' => 'Lorem ipsum dolor sit amet, Olympic size swimming pool and sports grounds.'],
                ['icon' => '🌍', 'title' => 'Global Exchange', 'desc' => 'Lorem ipsum dolor sit amet, international student exchange and MUN programs.'],
                ['icon' => '🎨', 'title' => 'Creative & Visual Arts', 'desc' => 'Lorem ipsum dolor sit amet, dedicated studios for music, dance and fine arts.'],
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
            $icon  = $this->e($b['icon'] ?? '🌟');
            $title = $this->e($b['title'] ?? '');
            $desc  = $this->e($b['desc'] ?? '');

            $cardsHtml .= <<<HTML
            <div class="pro-ib-card">
                <div class="pro-ib-icon">{$icon}</div>
                <h3 class="pro-ib-title">{$title}</h3>
                <p class="pro-ib-desc">{$desc}</p>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-ib-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; max-width: 1100px; margin: 30px auto 0; padding: 0 16px; }
        .pro-ib-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 28px; transition: all .3s ease; box-shadow: 0 8px 24px rgba(11,37,69,.05); position: relative; overflow: hidden; }
        .pro-ib-card:hover { border-color: #c79a3b; transform: translateY(-6px); box-shadow: 0 16px 36px rgba(11,37,69,.12); }
        .pro-ib-icon { font-size: 40px; margin-bottom: 16px; display: inline-block; transition: transform .3s ease; }
        .pro-ib-card:hover .pro-ib-icon { transform: scale(1.15) rotate(5deg); }
        .pro-ib-title { font-size: 18px; font-weight: 800; color: #0b2545; margin: 0 0 8px; }
        .pro-ib-desc { font-size: 14px; color: #64748b; margin: 0; line-height: 1.6; }
        </style>

        <section class="pro-ib-sec">
            {$head}
            <div class="pro-ib-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
