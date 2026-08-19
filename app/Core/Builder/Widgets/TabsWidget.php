<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Advanced Content Tabs.
 * Multi-tab content switcher with smooth tab switching.
 */
class TabsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-tabs';
    }

    public function label(): string
    {
        return 'Advanced Tabs';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Curriculum Wings',
            'heading' => 'Academic Programs & Methodology',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Click tabs to explore curriculum highlights.',
            'tabs'    => [
                [
                    'title'   => 'Foundational Wing (KG - II)',
                    'icon'    => '🌱',
                    'heading' => 'Play-based Experiential Learning',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Our foundational stage focuses on cognitive development, motor skills, sensory activities, storytelling and phonics through joyful play.',
                    'bullets' => ['Activity-based Montessori Method', 'Phonics & Language Lab', 'Safe & Soft Play Area', 'Individual Child Progress Tracker'],
                ],
                [
                    'title'   => 'Preparatory Wing (III - V)',
                    'icon'    => '📚',
                    'heading' => 'Building Conceptual Strength',
                    'content' => 'Transition from play to formal learning with structured inquiry in Science, Mathematics, Environmental Studies, Languages and Computer Literacy.',
                    'bullets' => ['Smart Classroom Presentations', 'Math & Science Activity Kits', 'Robotics & Coding Starters', 'Regular Field Trips & Excursions'],
                ],
                [
                    'title'   => 'Middle Wing (VI - VIII)',
                    'icon'    => '🔬',
                    'heading' => 'Exploration & STEM Integration',
                    'content' => 'Empowering critical thinking, experimental learning in advanced science labs, foreign languages and competitive exam orientation.',
                    'bullets' => ['Advanced Physics & Chemistry Labs', 'Foreign Language Electives', 'Olympiad & NTSE Coaching', 'Inter-School Debating & Quiz Clubs'],
                ],
                [
                    'title'   => 'Senior Secondary (IX - XII)',
                    'icon'    => '🎓',
                    'heading' => 'Career Foundation & Excellence',
                    'content' => 'CBSE curriculum integrated with specialized coaching for JEE, NEET, CUET and CLAT entrance examinations led by expert mentors.',
                    'bullets' => ['Integrated Competitive Preparation', 'Psychometric Career Guidance', 'National Level MUN & Tech Fests', '100% University Placement Record'],
                ],
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

        $tabs   = (array) $this->setting($settings, 'tabs', []);
        $tabsId = 'ek-tab-' . uniqid();

        $navHtml     = '';
        $panelsHtml  = '';

        foreach ($tabs as $idx => $t) {
            $title   = $this->e($t['title'] ?? 'Tab');
            $icon    = $this->e($t['icon'] ?? '📁');
            $heading = $this->e($t['heading'] ?? '');
            $content = $this->e($t['content'] ?? '');
            $active  = $idx === 0 ? 'active' : '';

            $navHtml .= <<<HTML
            <button type="button" class="ek-tab-btn {$active}" onclick="switchEkTab('{$tabsId}', {$idx})">
                <span class="ek-tab-icon">{$icon}</span>
                <span>{$title}</span>
            </button>
            HTML;

            $bulletsHtml = '';
            foreach ((array) ($t['bullets'] ?? []) as $b) {
                $bulletsHtml .= '<li><svg class="ek-tab-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg> ' . $this->e($b) . '</li>';
            }

            $panelsHtml .= <<<HTML
            <div class="ek-tab-panel {$active}" id="{$tabsId}-panel-{$idx}">
                <h3 class="ek-panel-head">{$heading}</h3>
                <p class="ek-panel-desc">{$content}</p>
                <ul class="ek-panel-bullets">
                    {$bulletsHtml}
                </ul>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-tabs-container { max-width: 1000px; margin: 30px auto 0; padding: 0 16px; }
        .ek-tabs-nav { display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; overflow-x: auto; padding-bottom: 2px; }
        .ek-tab-btn { display: flex; align-items: center; gap: 8px; padding: 14px 20px; font-size: 14.5px; font-weight: 600; color: #64748b; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: all .2s; white-space: nowrap; font-family: inherit; margin-bottom: -2px; }
        .ek-tab-btn:hover { color: #0b2545; background: #f8fafc; }
        .ek-tab-btn.active { color: #0b2545; border-bottom-color: #c79a3b; background: rgba(199,154,59,.08); border-radius: 8px 8px 0 0; }
        .ek-tab-icon { font-size: 18px; }
        .ek-tabs-body { background: #ffffff; border: 1.5px solid #e2e8f0; border-top: none; border-radius: 0 0 16px 16px; padding: 32px; box-shadow: 0 10px 28px rgba(11,37,69,.06); }
        .ek-tab-panel { display: none; }
        .ek-tab-panel.active { display: block; animation: ek-fade-in .3s ease; }
        .ek-panel-head { font-size: 22px; font-weight: 700; color: #0b2545; margin: 0 0 12px; }
        .ek-panel-desc { font-size: 15px; color: #475569; line-height: 1.65; margin: 0 0 24px; }
        .ek-panel-bullets { list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
        .ek-panel-bullets li { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: #0b2545; background: #f8fafc; padding: 10px 14px; border-radius: 10px; border: 1px solid #f1f5f9; }
        .ek-tab-check { width: 16px; height: 16px; color: #16a34a; flex-shrink: 0; }
        @keyframes ek-fade-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        </style>

        <section class="ek-tabs-sec">
            {$head}
            <div class="ek-tabs-container" id="{$tabsId}">
                <div class="ek-tabs-nav">
                    {$navHtml}
                </div>
                <div class="ek-tabs-body">
                    {$panelsHtml}
                </div>
            </div>
        </section>

        <script>
        function switchEkTab(containerId, activeIdx) {
            var container = document.getElementById(containerId);
            if (!container) return;
            var btns = container.querySelectorAll('.ek-tab-btn');
            var panels = container.querySelectorAll('.ek-tab-panel');
            btns.forEach(function(b, idx) { b.classList.toggle('active', idx === activeIdx); });
            panels.forEach(function(p, idx) { p.classList.toggle('active', idx === activeIdx); });
        }
        </script>
        HTML;
    }
}
