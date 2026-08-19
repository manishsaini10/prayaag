<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Advanced Dual-State Content Toggle.
 * Switcher button for toggling between two content states (e.g. Monthly/Annual).
 */
class AdvancedToggleWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-advanced-toggle';
    }

    public function label(): string
    {
        return 'Advanced Toggle';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'option_a_label' => 'Standard Curriculum',
            'option_a_title' => 'CBSE Core Curriculum & Experiential Learning',
            'option_a_desc'  => 'Lorem ipsum dolor sit amet, offering foundational learning, smart classrooms, arts, music and sports activities.',
            'option_b_label' => 'Integrated Coaching Wing',
            'option_b_title' => 'CBSE + Integrated JEE / NEET Competitive Prep',
            'option_b_desc'  => 'Specialized intensive coaching by Kota expert faculty integrated directly into school hours with mock test analytics.',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $labelA = $this->e($this->setting($settings, 'option_a_label', 'Option A'));
        $titleA = $this->e($this->setting($settings, 'option_a_title', 'Title A'));
        $descA  = $this->e($this->setting($settings, 'option_a_desc', 'Description A'));

        $labelB = $this->e($this->setting($settings, 'option_b_label', 'Option B'));
        $titleB = $this->e($this->setting($settings, 'option_b_title', 'Title B'));
        $descB  = $this->e($this->setting($settings, 'option_b_desc', 'Description B'));

        $toggleId = 'ek-at-' . uniqid();

        return <<<HTML
        <style>
        .ek-at-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 36px; max-width: 800px; margin: 30px auto; text-align: center; box-shadow: 0 12px 32px rgba(11,37,69,.06); }
        .ek-at-bar { display: inline-flex; align-items: center; background: #f1f5f9; padding: 4px; border-radius: 999px; margin-bottom: 28px; border: 1px solid #e2e8f0; }
        .ek-at-btn { padding: 10px 24px; border-radius: 999px; border: none; background: none; font-size: 14px; font-weight: 700; color: #64748b; cursor: pointer; transition: all .25s ease; font-family: inherit; }
        .ek-at-btn.active { background: #0b2545; color: #ffffff; box-shadow: 0 4px 12px rgba(11,37,69,.25); }
        .ek-at-panel { display: none; text-align: left; background: #f8fafc; border-radius: 16px; padding: 28px; border: 1px solid #f1f5f9; }
        .ek-at-panel.active { display: block; animation: ek-at-fade .3s ease; }
        .ek-at-title { font-size: 22px; font-weight: 800; color: #0b2545; margin: 0 0 10px; }
        .ek-at-desc { font-size: 15px; color: #475569; line-height: 1.65; margin: 0; }
        @keyframes ek-at-fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        </style>

        <div class="ek-at-card" id="{$toggleId}">
            <div class="ek-at-bar">
                <button type="button" class="ek-at-btn active" id="{$toggleId}-btn-a" onclick="switchEkToggle('{$toggleId}', 'a')">{$labelA}</button>
                <button type="button" class="ek-at-btn" id="{$toggleId}-btn-b" onclick="switchEkToggle('{$toggleId}', 'b')">{$labelB}</button>
            </div>
            <div class="ek-at-panel active" id="{$toggleId}-panel-a">
                <h3 class="ek-at-title">{$titleA}</h3>
                <p class="ek-at-desc">{$descA}</p>
            </div>
            <div class="ek-at-panel" id="{$toggleId}-panel-b">
                <h3 class="ek-at-title">{$titleB}</h3>
                <p class="ek-at-desc">{$descB}</p>
            </div>
        </div>

        <script>
        function switchEkToggle(toggleId, state) {
            var btnA   = document.getElementById(toggleId + '-btn-a');
            var btnB   = document.getElementById(toggleId + '-btn-b');
            var panelA = document.getElementById(toggleId + '-panel-a');
            var panelB = document.getElementById(toggleId + '-panel-b');
            if (state === 'a') {
                btnA.classList.add('active'); btnB.classList.remove('active');
                panelA.classList.add('active'); panelB.classList.remove('active');
            } else {
                btnB.classList.add('active'); btnA.classList.remove('active');
                panelB.classList.add('active'); panelA.classList.remove('active');
            }
        }
        </script>
        HTML;
    }
}
