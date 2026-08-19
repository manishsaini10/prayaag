<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Stacked Cards Deck.
 * Overlapping card deck that expands on hover with smooth 3D offsets.
 */
class StackedCardsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-stacked-cards';
    }

    public function label(): string
    {
        return 'Stacked Cards Deck';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Campus Life Highlights',
            'heading' => 'Experience Life at Prayaag',
            'cards'   => [
                ['title' => 'Robotics & AI Innovation Hub', 'desc' => 'Hands-on training in micro-controllers, 3D printing and automation for young scientists.', 'tag' => 'Technology'],
                ['title' => 'Olympic Swimming Complex', 'desc' => 'All-season temperature controlled pool with international certified coaches.', 'tag' => 'Sports'],
                ['title' => 'Global Exchange & MUN Programs', 'desc' => 'Fostering international diplomacy, speech and debate leadership skills.', 'tag' => 'Leadership'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $cards = (array) $this->setting($settings, 'cards', []);
        $cardsHtml = '';

        foreach ($cards as $idx => $c) {
            $title = $this->e($c['title'] ?? '');
            $desc  = $this->e($c['desc'] ?? '');
            $tag   = $this->e($c['tag'] ?? 'Feature');
            $offset = $idx * 16;

            $cardsHtml .= <<<HTML
            <div class="ek-sc-card" style="--idx: {$idx}; --offset: {$offset}px;">
                <span class="ek-sc-tag">{$tag}</span>
                <h3 class="ek-sc-title">{$title}</h3>
                <p class="ek-sc-desc">{$desc}</p>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-sc-container { position: relative; max-width: 650px; height: 320px; margin: 40px auto 0; padding: 0 16px; }
        .ek-sc-card { position: absolute; inset: 0; width: 100%; height: 260px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 28px; box-shadow: 0 12px 32px rgba(11,37,69,.1); transform: translateY(var(--offset)) scale(calc(1 - (var(--idx) * 0.04))); transition: all .4s cubic-bezier(.2,.7,.2,1); cursor: pointer; }
        .ek-sc-container:hover .ek-sc-card { transform: translateY(calc(var(--idx) * 60px)) scale(1); box-shadow: 0 16px 40px rgba(11,37,69,.15); }
        .ek-sc-tag { display: inline-block; background: #fdf6e2; color: #c79a3b; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; text-transform: uppercase; margin-bottom: 12px; }
        .ek-sc-title { font-size: 20px; font-weight: 800; color: #0b2545; margin: 0 0 10px; }
        .ek-sc-desc { font-size: 14px; color: #64748b; margin: 0; line-height: 1.6; }
        </style>

        <section class="ek-sc-sec">
            {$head}
            <div class="ek-sc-container">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
