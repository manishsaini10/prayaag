<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Business / Office Hours Card.
 * Timetable schedule card for school office & enquiry desk with status badge.
 */
class BusinessHoursWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-business-hours';
    }

    public function label(): string
    {
        return 'Business / Office Hours';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Campus Visitor Hours',
            'heading' => 'School Office & Enquiry Schedule',
            'hours'   => [
                ['day' => 'Monday – Friday', 'time' => '08:00 AM – 04:00 PM', 'status' => 'Open'],
                ['day' => 'Saturday', 'time' => '09:00 AM – 01:30 PM', 'status' => 'Half Day'],
                ['day' => 'Sunday & Gazetted Holidays', 'time' => 'Closed', 'status' => 'Closed'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $hours = (array) $this->setting($settings, 'hours', []);
        $rowsHtml = '';

        foreach ($hours as $h) {
            $day    = $this->e($h['day'] ?? '');
            $time   = $this->e($h['time'] ?? '');
            $status = $this->e($h['status'] ?? 'Open');

            $badgeClass = match (strtolower($status)) {
                'open' => 'pro-bh-badge-open',
                'closed' => 'pro-bh-badge-closed',
                default => 'pro-bh-badge-half',
            };

            $rowsHtml .= <<<HTML
            <div class="pro-bh-row">
                <span class="pro-bh-day">{$day}</span>
                <span class="pro-bh-dots"></span>
                <span class="pro-bh-time">{$time}</span>
                <span class="pro-bh-badge {$badgeClass}">{$status}</span>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-bh-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 20px; padding: 32px; max-width: 650px; margin: 30px auto; box-shadow: 0 10px 30px rgba(11,37,69,.06); }
        .pro-bh-row { display: flex; align-items: center; gap: 12px; padding: 14px 0; border-bottom: 1px dashed #e2e8f0; }
        .pro-bh-row:last-child { border-bottom: none; }
        .pro-bh-day { font-size: 15px; font-weight: 700; color: #0b2545; white-space: nowrap; }
        .pro-bh-dots { flex: 1; border-bottom: 2px dotted #cbd5e1; height: 1px; }
        .pro-bh-time { font-size: 14px; font-weight: 600; color: #475569; white-space: nowrap; }
        .pro-bh-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; text-transform: uppercase; white-space: nowrap; }
        .pro-bh-badge-open { background: rgba(16,185,129,.12); color: #10b981; }
        .pro-bh-badge-closed { background: rgba(239,68,68,.12); color: #ef4444; }
        .pro-bh-badge-half { background: rgba(245,158,11,.12); color: #f59e0b; }
        </style>

        <section class="pro-bh-sec">
            {$head}
            <div class="pro-bh-card">
                {$rowsHtml}
            </div>
        </section>
        HTML;
    }
}
