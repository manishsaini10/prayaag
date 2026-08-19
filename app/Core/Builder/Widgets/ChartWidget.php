<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Chart & Statistics.
 * Visual SVG bar/column performance chart with custom data metrics.
 */
class ChartWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-chart';
    }

    public function label(): string
    {
        return 'Chart & Statistics';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Annual Performance Metrics',
            'heading' => 'Class X & XII Board Examination Results (2020 - 2025)',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Audited pass percentage and distinction ratios.',
            'series'  => [
                ['label' => '2021', 'val' => 96, 'color' => '#0b2545'],
                ['label' => '2022', 'val' => 98, 'color' => '#1c3a6e'],
                ['label' => '2023', 'val' => 97, 'color' => '#3b82f6'],
                ['label' => '2024', 'val' => 99, 'color' => '#10b981'],
                ['label' => '2025', 'val' => 100, 'color' => '#c79a3b'],
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
        $series = (array) $this->setting($settings, 'series', []);

        foreach ($series as $s) {
            $lbl   = $this->e($s['label'] ?? 'Year');
            $val   = max(0, min(100, (int) ($s['val'] ?? 50)));
            $color = $this->e($s['color'] ?? '#0b2545');

            $barsHtml .= <<<HTML
            <div class="ek-chart-col">
                <div class="ek-chart-bar-wrap">
                    <div class="ek-chart-val">{$val}%</div>
                    <div class="ek-chart-bar" style="height: {$val}%; background: {$color};"></div>
                </div>
                <div class="ek-chart-lbl">{$lbl}</div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-chart-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 36px 24px; max-width: 900px; margin: 30px auto; box-shadow: 0 10px 30px rgba(11,37,69,.06); }
        .ek-chart-flex { display: flex; align-items: flex-end; justify-content: space-around; gap: 16px; height: 260px; padding-top: 24px; border-bottom: 2px solid #e2e8f0; }
        .ek-chart-col { flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; }
        .ek-chart-bar-wrap { flex: 1; width: 100%; max-width: 54px; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; position: relative; }
        .ek-chart-val { font-size: 12px; font-weight: 700; color: #0b2545; margin-bottom: 6px; font-family: ui-monospace, monospace; }
        .ek-chart-bar { width: 100%; border-radius: 8px 8px 0 0; transition: height .8s ease-in-out; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        .ek-chart-lbl { font-size: 13px; font-weight: 700; color: #64748b; margin-top: 12px; }
        </style>

        <section class="ek-chart-sec">
            {$head}
            <div class="ek-chart-card">
                <div class="ek-chart-flex">
                    {$barsHtml}
                </div>
            </div>
        </section>
        HTML;
    }
}
