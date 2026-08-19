<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Mega Menu Navigation.
 * Multi-column mega dropdown navigation card for header or sitemap footers.
 */
class MegaMenuWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-mega-menu';
    }

    public function label(): string
    {
        return 'Mega Menu Navigation';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Academic Departments & Programs',
            'cols'  => [
                [
                    'cat'   => 'Primary & Middle Wing',
                    'links' => ['Foundation Stage (Nursery-KG)', 'Primary School (Grade I-V)', 'Middle School (Grade VI-VIII)', 'Activity-Based STEM'],
                ],
                [
                    'cat'   => 'Senior Secondary Wing',
                    'links' => ['Science Stream (PCM / PCB)', 'Commerce & Economics', 'Humanities & Legal Studies', 'Integrated JEE / NEET Coaching'],
                ],
                [
                    'cat'   => 'Co-Curricular & Sports',
                    'links' => ['Olympic Swimming Pool', 'Equestrian & Horse Riding', 'Music, Drama & Fine Arts', 'Robotics & 3D Printing Lab'],
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $title = $this->e($this->setting($settings, 'title'));
        $cols  = (array) $this->setting($settings, 'cols', []);
        $colsHtml = '';

        foreach ($cols as $c) {
            $cat   = $this->e($c['cat'] ?? '');
            $links = (array) ($c['links'] ?? []);
            $linksHtml = '';

            foreach ($links as $l) {
                $lStr = $this->e($l);
                $linksHtml .= <<<HTML
                <li><a href="#" class="pro-mm-link">{$lStr}</a></li>
                HTML;
            }

            $colsHtml .= <<<HTML
            <div class="pro-mm-col">
                <h4 class="pro-mm-cat">{$cat}</h4>
                <ul class="pro-mm-list">
                    {$linksHtml}
                </ul>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-mm-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 32px; max-width: 1000px; margin: 30px auto; box-shadow: 0 16px 40px rgba(11,37,69,.08); }
        .pro-mm-head { font-size: 20px; font-weight: 800; color: #0b2545; margin: 0 0 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; }
        .pro-mm-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 32px; }
        .pro-mm-cat { font-size: 15px; font-weight: 800; color: #c79a3b; margin: 0 0 14px; text-transform: uppercase; letter-spacing: .5px; }
        .pro-mm-list { list-style: none; padding: 0; margin: 0; }
        .pro-mm-list li { margin-bottom: 10px; }
        .pro-mm-link { color: #475569; font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s ease; display: inline-block; }
        .pro-mm-link:hover { color: #0b2545; transform: translateX(4px); }
        </style>

        <div class="pro-mm-card">
            <h3 class="pro-mm-head">{$title}</h3>
            <div class="pro-mm-grid">
                {$colsHtml}
            </div>
        </div>
        HTML;
    }
}
