<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Circle Menu.
 * Radial circular expanding menu with central action toggle button.
 */
class CircleMenuWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-circle-menu';
    }

    public function label(): string
    {
        return 'Circle Menu';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Quick Action Radial Hub',
            'heading' => 'Interactive Navigation Wheel',
            'sub'     => 'Click the central button to expand quick action links.',
            'items'   => [
                ['icon' => '🎓', 'label' => 'Admissions', 'url' => '/admissions'],
                ['icon' => '📞', 'label' => 'Call Us', 'url' => 'tel:01802571100'],
                ['icon' => '📩', 'label' => 'Enquiry', 'url' => '/contact'],
                ['icon' => '📍', 'label' => 'Campus Map', 'url' => '/facilities'],
                ['icon' => '📑', 'label' => 'Prospectus', 'url' => '/downloads'],
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

        $items = (array) $this->setting($settings, 'items', []);
        $itemsHtml = '';
        $total = count($items);
        $cmId = 'ek-cm-' . uniqid();

        foreach ($items as $idx => $it) {
            $icon  = $this->e($it['icon'] ?? '⚡');
            $lbl   = $this->e($it['label'] ?? 'Link');
            $url   = $this->e($it['url'] ?? '#');
            $angle = ($idx * (360 / max(1, $total))) - 90;

            $itemsHtml .= <<<HTML
            <a href="{$url}" class="ek-cm-item" style="--angle: {$angle}deg;" title="{$lbl}">
                <span class="ek-cm-icon">{$icon}</span>
                <span class="ek-cm-tooltip">{$lbl}</span>
            </a>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-cm-wrap { position: relative; width: 280px; height: 280px; margin: 40px auto; display: flex; align-items: center; justify-content: center; }
        .ek-cm-toggle { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #c79a3b; border: 3px solid #ffffff; font-size: 28px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 28px rgba(11,37,69,.3); z-index: 10; transition: transform .3s; }
        .ek-cm-wrap.active .ek-cm-toggle { transform: rotate(45deg); background: #c79a3b; color: #0b2545; }
        .ek-cm-item { position: absolute; width: 50px; height: 50px; border-radius: 50%; background: #ffffff; border: 1.5px solid #e2e8f0; color: #0b2545; display: flex; align-items: center; justify-content: center; font-size: 20px; text-decoration: none; box-shadow: 0 6px 16px rgba(0,0,0,.1); transition: all .4s cubic-bezier(.175, .885, .32, 1.275); opacity: 0; transform: translate(0, 0) scale(.5); pointer-events: none; }
        .ek-cm-wrap.active .ek-cm-item { opacity: 1; pointer-events: auto; transform: rotate(var(--angle)) translate(110px) rotate(calc(-1 * var(--angle))) scale(1); }
        .ek-cm-item:hover { background: #0b2545; color: #ffffff; border-color: #0b2545; transform: rotate(var(--angle)) translate(110px) rotate(calc(-1 * var(--angle))) scale(1.15) !important; }
        .ek-cm-tooltip { position: absolute; bottom: -24px; font-size: 11px; font-weight: 700; color: #0b2545; white-space: nowrap; background: #ffffff; padding: 2px 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.15); opacity: 0; transition: opacity .2s; pointer-events: none; }
        .ek-cm-item:hover .ek-cm-tooltip { opacity: 1; }
        </style>

        <section class="ek-cm-sec">
            {$head}
            <div class="ek-cm-wrap active" id="{$cmId}">
                <button type="button" class="ek-cm-toggle" onclick="document.getElementById('{$cmId}').classList.toggle('active')">★</button>
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
