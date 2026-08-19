<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Interactive Event Calendar Grid.
 * Displays calendar month grid with marked event dates and event info cards.
 */
class EventCalendarWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-event-calendar';
    }

    public function label(): string
    {
        return 'Event Calendar';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Academic Schedule',
            'heading' => 'Upcoming School Calendar & Events',
            'month'   => 'August 2026',
            'events'  => [
                ['date' => '15 Aug', 'title' => 'Independence Day Flag Hoisting & Cultural Fest', 'time' => '08:00 AM', 'badge' => 'National Event'],
                ['date' => '22 Aug', 'title' => 'Inter-House Football Tournament & Athletics Finals', 'time' => '09:30 AM', 'badge' => 'Sports'],
                ['date' => '28 Aug', 'title' => 'Parent-Teacher Interaction (Mid-Term Assessment)', 'time' => '09:00 AM', 'badge' => 'Academic'],
                ['date' => '05 Sep', 'title' => 'Teachers Day Celebration & Student Council Takeover', 'time' => '10:00 AM', 'badge' => 'Celebration'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $month  = $this->e($this->setting($settings, 'month', 'August 2026'));
        $events = (array) $this->setting($settings, 'events', []);

        $listHtml = '';
        foreach ($events as $ev) {
            $dt    = $this->e($ev['date'] ?? '');
            $title = $this->e($ev['title'] ?? '');
            $time  = $this->e($ev['time'] ?? '');
            $badge = $this->e($ev['badge'] ?? 'Event');

            $listHtml .= <<<HTML
            <div class="ek-ec-item">
                <div class="ek-ec-date-box">
                    <span class="ek-ec-dt">{$dt}</span>
                </div>
                <div class="ek-ec-details">
                    <div class="ek-ec-meta">
                        <span class="ek-ec-badge">{$badge}</span>
                        <span class="ek-ec-time">⏰ {$time}</span>
                    </div>
                    <h3 class="ek-ec-title">{$title}</h3>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-ec-container { max-width: 900px; margin: 30px auto 0; padding: 0 16px; }
        .ek-ec-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
        .ek-ec-month { font-size: 20px; font-weight: 800; color: #0b2545; }
        .ek-ec-list { display: flex; flex-direction: column; gap: 14px; }
        .ek-ec-item { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; display: flex; align-items: center; gap: 20px; transition: all .25s ease; box-shadow: 0 4px 14px rgba(11,37,69,.04); }
        .ek-ec-item:hover { border-color: #c79a3b; transform: translateX(6px); box-shadow: 0 8px 24px rgba(11,37,69,.1); }
        .ek-ec-date-box { width: 70px; height: 70px; border-radius: 12px; background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #ffffff; display: flex; align-items: center; justify-content: center; text-align: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(11,37,69,.2); }
        .ek-ec-dt { font-size: 15px; font-weight: 800; line-height: 1.2; text-transform: uppercase; color: #c79a3b; }
        .ek-ec-details { flex: 1; }
        .ek-ec-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
        .ek-ec-badge { background: #fdf6e2; color: #c79a3b; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; text-transform: uppercase; }
        .ek-ec-time { font-size: 12px; color: #64748b; font-weight: 500; }
        .ek-ec-title { font-size: 16px; font-weight: 700; color: #0b2545; margin: 0; line-height: 1.4; }
        </style>

        <section class="ek-ec-sec">
            {$head}
            <div class="ek-ec-container">
                <div class="ek-ec-header">
                    <span class="ek-ec-month">📅 {$month}</span>
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">Official School Calendar</span>
                </div>
                <div class="ek-ec-list">
                    {$listHtml}
                </div>
            </div>
        </section>
        HTML;
    }
}
