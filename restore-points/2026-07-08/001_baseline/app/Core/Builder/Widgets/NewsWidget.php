<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * News list + calendar call-out. Defaults carry the existing home-page updates.
 */
class NewsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'news-calendar';
    }

    public function label(): string
    {
        return 'News + Calendar';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow'    => 'Events & News',
            'heading'    => 'News From The Campus',
            'list_title' => 'Latest Updates',
            'items'      => [
                ['text' => 'Scholarship Exam Results', 'url' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/03/SCHOLARSHIP-EXAM-1.pdf'],
                ['text' => 'Online career counselling drive for Grade IX to XII', 'url' => ''],
                ['text' => 'Regular parents counselling', 'url' => ''],
                ['text' => 'Regular student counselling', 'url' => ''],
                ['text' => 'Vaccination Drive for age 15 to 18', 'url' => ''],
            ],
            'cal_title'  => 'Check the Full Calendar for Upcoming Events',
            'cal_text'   => 'Stay up to date with everything happening across the Prayaag campus this academic year.',
            'cal_label'  => 'Upcoming Events →',
            'cal_url'    => '/events/',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead($this->setting($settings, 'eyebrow'), $this->setting($settings, 'heading'));

        $listTitle = $this->e($this->setting($settings, 'list_title', 'Latest Updates'));
        $rows = '';
        foreach ((array) $this->setting($settings, 'items', []) as $item) {
            $text = $this->e($item['text'] ?? '');
            $url  = $item['url'] ?? '';
            $rows .= '<li>' . ($url ? '<a href="' . $this->e($url) . '">' . $text . '</a>' : $text) . '</li>';
        }

        $calTitle = $this->e($this->setting($settings, 'cal_title'));
        $calText  = $this->e($this->setting($settings, 'cal_text'));
        $calLabel = $this->e($this->setting($settings, 'cal_label', 'View Calendar'));
        $calUrl   = $this->e($this->setting($settings, 'cal_url', '/events/'));

        return $head . <<<HTML
        <div class="news-grid">
            <div class="news-card" data-reveal data-reveal-delay="1">
                <div class="news-head">{$listTitle}</div>
                <ul class="news-list">{$rows}</ul>
            </div>
            <div class="cal-card" data-reveal data-reveal-delay="2">
                <h3>{$calTitle}</h3>
                <p>{$calText}</p>
                <a class="btn btn-gold" href="{$calUrl}">{$calLabel}</a>
            </div>
        </div>
        HTML;
    }
}
