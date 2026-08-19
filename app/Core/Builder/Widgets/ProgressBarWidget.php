<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Animated Progress Bars / Skill Meters.
 * Features progress bars with animated percentages and labels.
 */
class ProgressBarWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-progress';
    }

    public function label(): string
    {
        return 'Progress Bars';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Benchmark Statistics',
            'heading' => 'Performance & Excellence Ratings',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Audited metrics reflecting our educational standards.',
            'bars'    => [
                ['label' => 'Board Exam Pass Rate (Class X & XII)', 'percentage' => 100],
                ['label' => 'Qualified & Experienced Faculty', 'percentage' => 98],
                ['label' => 'Parents Satisfaction Index', 'percentage' => 96],
                ['label' => 'Competitive Entrance Selection Rate', 'percentage' => 92],
                ['label' => 'Sports & Extracurricular Participation', 'percentage' => 95],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $barsHtml = '';
        $bars = (array) $this->setting($settings, 'bars', []);

        foreach ($bars as $b) {
            $label = $this->e($b['label'] ?? 'Skill');
            $pct   = max(0, min(100, (int) ($b['percentage'] ?? 50)));

            $barsHtml .= <<<HTML
            <div class="ek-pb-item">
                <div class="ek-pb-header">
                    <span class="ek-pb-label">{$label}</span>
                    <span class="ek-pb-pct">{$pct}%</span>
                </div>
                <div class="ek-pb-track">
                    <div class="ek-pb-fill" style="width: {$pct}%;"></div>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-pb-wrapper { max-width: 800px; margin: 30px auto 0; padding: 0 16px; }
        .ek-pb-item { margin-bottom: 22px; }
        .ek-pb-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .ek-pb-label { font-size: 15px; font-weight: 600; color: #0b2545; }
        .ek-pb-pct { font-size: 14px; font-weight: 700; color: #c79a3b; font-family: ui-monospace, monospace; }
        .ek-pb-track { height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; position: relative; }
        .ek-pb-fill { height: 100%; background: linear-gradient(90deg, #0b2545 0%, #1c3a6e 60%, #c79a3b 100%); border-radius: 999px; transition: width 1s ease-in-out; }
        </style>

        <section class="ek-pb-sec">
            {$head}
            <div class="ek-pb-wrapper">
                {$barsHtml}
            </div>
        </section>
        HTML;
    }
}
