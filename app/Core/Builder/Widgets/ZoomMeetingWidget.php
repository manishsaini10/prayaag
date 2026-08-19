<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Zoom Online Meeting & Webinar Schedule.
 * Card displaying upcoming Zoom session details with direct join CTA.
 */
class ZoomMeetingWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-zoom-meeting';
    }

    public function label(): string
    {
        return 'Zoom Meeting Schedule';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'topic'       => 'Live Interactive Orientation & Career Counselling Webinar',
            'host'        => 'Dr. Rajesh Sharma (Principal)',
            'date_time'   => 'Saturday, 3:00 PM - 4:30 PM',
            'meeting_id'  => '849 2016 7789',
            'passcode'    => 'PRAYAAG2026',
            'join_url'    => 'https://zoom.us',
            'badge'       => 'Live Webinar',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $topic     = $this->e($this->setting($settings, 'topic'));
        $host      = $this->e($this->setting($settings, 'host'));
        $dateTime  = $this->e($this->setting($settings, 'date_time'));
        $meetingId = $this->e($this->setting($settings, 'meeting_id'));
        $passcode  = $this->e($this->setting($settings, 'passcode'));
        $joinUrl   = $this->e($this->setting($settings, 'join_url', '#'));
        $badge     = $this->e($this->setting($settings, 'badge', 'Live Session'));

        return <<<HTML
        <style>
        .ek-zm-card { background: linear-gradient(135deg, #2d8cff, #0e71eb); color: #ffffff; border-radius: 20px; padding: 32px; max-width: 600px; margin: 30px auto; box-shadow: 0 16px 40px rgba(45,140,255,.25); position: relative; overflow: hidden; }
        .ek-zm-badge { display: inline-block; background: rgba(255,255,255,.2); backdrop-filter: blur(4px); color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 14px; border-radius: 999px; margin-bottom: 16px; border: 1px solid rgba(255,255,255,.3); }
        .ek-zm-topic { font-size: 22px; font-weight: 800; color: #ffffff; margin: 0 0 12px; line-height: 1.3; }
        .ek-zm-host { font-size: 14px; color: rgba(255,255,255,.9); margin-bottom: 24px; font-weight: 500; }
        .ek-zm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: rgba(0,0,0,.15); padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 13px; }
        .ek-zm-lbl { font-size: 11px; text-transform: uppercase; opacity: .75; letter-spacing: .5px; margin-bottom: 2px; }
        .ek-zm-val { font-weight: 700; font-family: ui-monospace, monospace; }
        .ek-zm-btn { display: inline-block; width: 100%; text-align: center; background: #ffffff; color: #0e71eb; font-size: 15px; font-weight: 800; padding: 14px; border-radius: 10px; text-decoration: none; box-shadow: 0 6px 20px rgba(0,0,0,.15); transition: transform .2s; }
        .ek-zm-btn:hover { transform: translateY(-2px); background: #f8fafc; }
        </style>

        <div class="ek-zm-card">
            <span class="ek-zm-badge">🎥 {$badge}</span>
            <h3 class="ek-zm-topic">{$topic}</h3>
            <div class="ek-zm-host">Hosted by <strong>{$host}</strong></div>
            <div class="ek-zm-grid">
                <div><div class="ek-zm-lbl">Date & Time</div><div class="ek-zm-val">{$dateTime}</div></div>
                <div><div class="ek-zm-lbl">Meeting ID</div><div class="ek-zm-val">{$meetingId}</div></div>
                <div><div class="ek-zm-lbl">Passcode</div><div class="ek-zm-val">{$passcode}</div></div>
                <div><div class="ek-zm-lbl">Platform</div><div class="ek-zm-val">Zoom Video</div></div>
            </div>
            <a href="{$joinUrl}" target="_blank" rel="noopener" class="ek-zm-btn">Join Zoom Meeting Now →</a>
        </div>
        HTML;
    }
}
