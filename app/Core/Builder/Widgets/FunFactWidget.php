<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Fun Fact / Animated Counter Grid.
 * Statistical counters with animated numbers and gold milestone badges.
 */
class FunFactWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-fun-fact';
    }

    public function label(): string
    {
        return 'Fun Fact / Animated Counter';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Milestones & Impact',
            'heading' => 'Prayaag by the Numbers',
            'facts'   => [
                ['number' => '100%', 'label' => 'Board Pass Percentage', 'icon' => '🎓'],
                ['number' => '15:1', 'label' => 'Student-Teacher Ratio', 'icon' => '👨‍🏫'],
                ['number' => '25+', 'label' => 'Olympiad & Gold Medals', 'icon' => '🥇'],
                ['number' => '10 Acres', 'label' => 'Lush Green Campus', 'icon' => '🌳'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $facts = (array) $this->setting($settings, 'facts', []);
        $cardsHtml = '';

        foreach ($facts as $f) {
            $num   = $this->e($f['number'] ?? '0');
            $lbl   = $this->e($f['label'] ?? '');
            $icon  = $this->e($f['icon'] ?? '📊');

            $cardsHtml .= <<<HTML
            <div class="pro-ff-card">
                <div class="pro-ff-icon">{$icon}</div>
                <div class="pro-ff-num">{$num}</div>
                <div class="pro-ff-lbl">{$lbl}</div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-ff-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; max-width: 1100px; margin: 30px auto 0; padding: 0 16px; }
        .pro-ff-card { background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #ffffff; border: 1.5px solid rgba(199,154,59,.4); border-radius: 20px; padding: 32px 20px; text-align: center; box-shadow: 0 12px 32px rgba(11,37,69,.15); transition: all .3s ease; }
        .pro-ff-card:hover { transform: translateY(-6px); border-color: #c79a3b; box-shadow: 0 18px 40px rgba(11,37,69,.25); }
        .pro-ff-icon { font-size: 36px; margin-bottom: 12px; }
        .pro-ff-num { font-size: 36px; font-weight: 900; color: #c79a3b; margin-bottom: 6px; font-family: ui-monospace, monospace; }
        .pro-ff-lbl { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.9); text-transform: uppercase; letter-spacing: .5px; }
        </style>

        <section class="pro-ff-sec">
            {$head}
            <div class="pro-ff-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
