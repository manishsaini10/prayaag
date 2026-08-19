<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Vertical History / Milestone Timeline.
 * Features central line with glowing nodes, year badges and alternating cards.
 */
class TimelineWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-timeline';
    }

    public function label(): string
    {
        return 'Milestone Timeline';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Our Journey',
            'heading' => 'A Decade of Academic Excellence',
            'sub'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Exploring the milestones that defined our institution.',
            'events'  => [
                [
                    'year'  => '2016',
                    'title' => 'School Foundation Laid',
                    'desc'  => 'Lorem ipsum dolor sit amet, founded with a vision to deliver global standards of education in Panipat.',
                    'badge' => 'Inauguration',
                ],
                [
                    'year'  => '2018',
                    'title' => 'CBSE Senior Secondary Affiliation',
                    'desc'  => 'Expanded to Science, Commerce & Humanities wings with state-of-the-art laboratories and smart classrooms.',
                    'badge' => 'Affiliation',
                ],
                [
                    'year'  => '2020',
                    'title' => 'British Council International Award',
                    'desc'  => 'Recognized internationally for fostering global dimension and collaborative learning in curriculum.',
                    'badge' => 'Global Honor',
                ],
                [
                    'year'  => '2022',
                    'title' => 'Stellar 100% Board Results & JEE Ranks',
                    'desc'  => 'Students achieved top percentile ranks in Class X & XII Board examinations and competitive entrances.',
                    'badge' => 'Academic Peak',
                ],
                [
                    'year'  => '2025',
                    'title' => 'Smart AI & Robotics Hub Launch',
                    'desc'  => 'Inaugurated 3D Printing, Artificial Intelligence and Robotics lab empowering future-ready skills.',
                    'badge' => 'Innovation',
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

        $itemsHtml = '';
        $events = (array) $this->setting($settings, 'events', []);

        foreach ($events as $idx => $ev) {
            $year  = $this->e($ev['year'] ?? 'Year');
            $title = $this->e($ev['title'] ?? 'Title');
            $desc  = $this->e($ev['desc'] ?? '');
            $badge = $this->e($ev['badge'] ?? '');

            $side = $idx % 2 === 0 ? 'left' : 'right';

            $itemsHtml .= <<<HTML
            <div class="ek-tl-item {$side}">
                <div class="ek-tl-node"></div>
                <div class="ek-tl-content">
                    <div class="ek-tl-header">
                        <span class="ek-tl-year">{$year}</span>
                        <span class="ek-tl-tag">{$badge}</span>
                    </div>
                    <h3 class="ek-tl-title">{$title}</h3>
                    <p class="ek-tl-desc">{$desc}</p>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-tl-wrapper { position: relative; max-width: 900px; margin: 40px auto 0; padding: 0 16px; }
        .ek-tl-wrapper::before { content: ''; position: absolute; top: 0; bottom: 0; left: 50%; width: 3px; background: linear-gradient(180deg, #c79a3b 0%, #0b2545 50%, #c79a3b 100%); transform: translateX(-50%); border-radius: 999px; }
        .ek-tl-item { position: relative; width: 50%; margin-bottom: 36px; }
        .ek-tl-item.left { left: 0; padding-right: 40px; text-align: right; }
        .ek-tl-item.right { left: 50%; padding-left: 40px; text-align: left; }
        .ek-tl-node { position: absolute; top: 18px; width: 16px; height: 16px; background: #c79a3b; border: 3px solid #ffffff; border-radius: 50%; box-shadow: 0 0 0 4px rgba(199,154,59,.3); z-index: 2; }
        .ek-tl-item.left .ek-tl-node { right: -8px; }
        .ek-tl-item.right .ek-tl-node { left: -8px; }
        .ek-tl-content { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 22px; transition: all .3s ease; box-shadow: 0 6px 20px rgba(11,37,69,.06); }
        .ek-tl-item:hover .ek-tl-content { border-color: #c79a3b; transform: translateY(-4px); box-shadow: 0 12px 30px rgba(11,37,69,.12); }
        .ek-tl-header { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .ek-tl-item.left .ek-tl-header { justify-content: flex-end; }
        .ek-tl-item.right .ek-tl-header { justify-content: flex-start; }
        .ek-tl-year { font-size: 18px; font-weight: 800; color: #0b2545; }
        .ek-tl-tag { background: #fdf6e2; color: #c79a3b; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; text-transform: uppercase; }
        .ek-tl-title { font-size: 16px; font-weight: 700; color: #0b2545; margin: 0 0 6px; }
        .ek-tl-desc { font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.55; }
        @media(max-width: 768px) {
            .ek-tl-wrapper::before { left: 20px; }
            .ek-tl-item { width: 100%; left: 0 !important; padding-left: 50px !important; padding-right: 0 !important; text-align: left !important; }
            .ek-tl-item .ek-tl-node { left: 12px !important; }
            .ek-tl-item .ek-tl-header { justify-content: flex-start !important; }
        }
        </style>

        <section class="ek-tl-sec">
            {$head}
            <div class="ek-tl-wrapper">
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
