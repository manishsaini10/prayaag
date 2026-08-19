<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Client / Sponsor Logo Carousel.
 * Infinite horizontal scrolling strip for affiliations, accreditation & partners.
 */
class ClientLogoWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-client-logo';
    }

    public function label(): string
    {
        return 'Client / Sponsor Logo Strip';
    }

    public function category(): string
    {
        return 'pro-social';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Recognitions & Accreditations',
            'heading' => 'Affiliated with India’s Premier Educational Bodies',
            'logos'   => [
                ['name' => 'CBSE Board Affiliated', 'badge' => 'CBSE Delhi'],
                ['name' => 'National Olympiad Foundation', 'badge' => 'Olympiad'],
                ['name' => 'ISO 9001:2024 Certified', 'badge' => 'ISO Quality'],
                ['name' => 'British Council International School', 'badge' => 'Global ISA'],
                ['name' => 'Robotics & STEM India Alliance', 'badge' => 'STEM Hub'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $logos = (array) $this->setting($settings, 'logos', []);
        $itemsHtml = '';

        foreach ($logos as $l) {
            $name  = $this->e($l['name'] ?? '');
            $badge = $this->e($l['badge'] ?? 'Partner');

            $itemsHtml .= <<<HTML
            <div class="pro-cl-card">
                <span class="pro-cl-icon">🏛️</span>
                <div>
                    <div class="pro-cl-name">{$name}</div>
                    <div class="pro-cl-badge">{$badge}</div>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-cl-sec { padding: 40px 0; }
        .pro-cl-track { display: flex; gap: 20px; overflow-x: auto; padding: 10px 16px 20px; max-width: 1100px; margin: 20px auto 0; scrollbar-width: thin; }
        .pro-cl-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px 24px; min-width: 220px; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 16px rgba(11,37,69,.05); transition: all .3s ease; flex-shrink: 0; }
        .pro-cl-card:hover { border-color: #c79a3b; transform: translateY(-3px); box-shadow: 0 10px 24px rgba(11,37,69,.1); }
        .pro-cl-icon { font-size: 28px; }
        .pro-cl-name { font-size: 14px; font-weight: 700; color: #0b2545; }
        .pro-cl-badge { font-size: 11px; color: #64748b; margin-top: 2px; }
        </style>

        <section class="pro-cl-sec">
            {$head}
            <div class="pro-cl-track">
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
