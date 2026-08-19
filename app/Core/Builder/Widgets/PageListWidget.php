<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Page List & Links.
 * Categorized page link list with icons and quick access badges.
 */
class PageListWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-page-list';
    }

    public function label(): string
    {
        return 'Page List & Links';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Quick Navigation',
            'heading' => 'Explore School Portals & Links',
            'links'   => [
                ['title' => 'Admissions & Fee Structure 2026', 'url' => '/admissions', 'tag' => 'Open'],
                ['title' => 'Academic Curriculum & CBSE Syllabus', 'url' => '/academics', 'tag' => 'Updated'],
                ['title' => 'Hostel & Residential Boarding Facilities', 'url' => '/hostel', 'tag' => 'Campus'],
                ['title' => 'School Mess Weekly Meal Plan', 'url' => '/mess-menu', 'tag' => 'PDF'],
                ['title' => 'Career & Faculty Recruitment Openings', 'url' => '/career', 'tag' => 'Hiring'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $links = (array) $this->setting($settings, 'links', []);
        $itemsHtml = '';

        foreach ($links as $l) {
            $title = $this->e($l['title'] ?? '');
            $url   = $this->e($l['url'] ?? '#');
            $tag   = $this->e($l['tag'] ?? 'Link');

            $itemsHtml .= <<<HTML
            <a href="{$url}" class="pro-pl-item">
                <span class="pro-pl-icon">📌</span>
                <span class="pro-pl-title">{$title}</span>
                <span class="pro-pl-tag">{$tag}</span>
                <span class="pro-pl-arrow">→</span>
            </a>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-pl-box { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 16px; max-width: 700px; margin: 30px auto; box-shadow: 0 10px 30px rgba(11,37,69,.06); }
        .pro-pl-item { display: flex; align-items: center; gap: 14px; padding: 14px 18px; border-radius: 12px; text-decoration: none; color: #0b2545; transition: all .2s ease; margin-bottom: 6px; }
        .pro-pl-item:last-child { margin-bottom: 0; }
        .pro-pl-item:hover { background: #fdf6e2; color: #c79a3b; transform: translateX(6px); }
        .pro-pl-icon { font-size: 16px; }
        .pro-pl-title { font-size: 15px; font-weight: 700; flex: 1; }
        .pro-pl-tag { font-size: 11px; font-weight: 700; background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; }
        .pro-pl-item:hover .pro-pl-tag { background: #c79a3b; color: #0b2545; }
        .pro-pl-arrow { font-size: 16px; font-weight: 800; opacity: .4; transition: opacity .2s; }
        .pro-pl-item:hover .pro-pl-arrow { opacity: 1; }
        </style>

        <section class="pro-pl-sec">
            {$head}
            <div class="pro-pl-box">
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
