<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Image Accordion.
 * Horizontal expanding image panel gallery with hover zoom & caption overlay.
 */
class ImageAccordionWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-image-accordion';
    }

    public function label(): string
    {
        return 'Image Accordion';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Campus Infrastructure',
            'heading' => 'Explore Our World-Class Spaces',
            'panels'  => [
                ['title' => 'Digital Smart Classrooms', 'image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80', 'tag' => 'Academics'],
                ['title' => 'Advanced Robotics & AI Hub', 'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&auto=format&fit=crop&q=80', 'tag' => 'Innovation'],
                ['title' => 'Olympic Swimming Complex', 'image' => 'https://images.unsplash.com/photo-1576610616656-d3aa5d1f4534?w=800&auto=format&fit=crop&q=80', 'tag' => 'Sports'],
                ['title' => 'Central Library & Research', 'image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=800&auto=format&fit=crop&q=80', 'tag' => 'Learning'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $panels = (array) $this->setting($settings, 'panels', []);
        $panelsHtml = '';

        foreach ($panels as $idx => $p) {
            $title = $this->e($p['title'] ?? '');
            $img   = $this->e($p['image'] ?? '');
            $tag   = $this->e($p['tag'] ?? 'Campus');
            $active = $idx === 0 ? 'active' : '';

            $panelsHtml .= <<<HTML
            <div class="pro-ia-panel {$active}" style="background-image: url('{$img}');">
                <div class="pro-ia-overlay">
                    <span class="pro-ia-tag">{$tag}</span>
                    <h3 class="pro-ia-title">{$title}</h3>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-ia-container { display: flex; height: 380px; gap: 14px; max-width: 1100px; margin: 30px auto 0; padding: 0 16px; border-radius: 20px; overflow: hidden; }
        .pro-ia-panel { flex: 1; background-size: cover; background-position: center; border-radius: 16px; position: relative; transition: flex .5s cubic-bezier(.25,1,.5,1); cursor: pointer; }
        .pro-ia-panel:hover, .pro-ia-panel.active { flex: 3; }
        .pro-ia-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(11,37,69,.9), transparent 60%); border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: flex-end; opacity: 0; transition: opacity .4s ease; }
        .pro-ia-panel:hover .pro-ia-overlay, .pro-ia-panel.active .pro-ia-overlay { opacity: 1; }
        .pro-ia-tag { display: inline-block; background: #c79a3b; color: #0b2545; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 6px; text-transform: uppercase; align-self: flex-start; margin-bottom: 8px; }
        .pro-ia-title { font-size: 20px; font-weight: 800; color: #ffffff; margin: 0; }
        @media(max-width: 768px) { .pro-ia-container { flex-direction: column; height: 500px; } }
        </style>

        <section class="pro-ia-sec">
            {$head}
            <div class="pro-ia-container">
                {$panelsHtml}
            </div>
        </section>
        HTML;
    }
}
