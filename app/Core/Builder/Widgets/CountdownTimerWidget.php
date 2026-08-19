<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Countdown Timer.
 * Features live ticking countdown clock with days, hours, minutes, seconds.
 */
class CountdownTimerWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-countdown';
    }

    public function label(): string
    {
        return 'Countdown Timer';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow'     => '★ Admissions Closing Soon',
            'heading'     => 'Limited Seats Available for Session 2026-27',
            'target_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'button_text' => 'Register Now Before Seats Fill Up →',
            'button_url'  => '/admissions',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $eyebrow    = $this->e($this->setting($settings, 'eyebrow'));
        $heading    = $this->e($this->setting($settings, 'heading'));
        $targetDate = $this->e($this->setting($settings, 'target_date', date('Y-m-d H:i:s', strtotime('+30 days'))));
        $btnText    = $this->e($this->setting($settings, 'button_text'));
        $btnUrl     = $this->e($this->setting($settings, 'button_url', '#'));

        $timerId = 'ek-timer-' . uniqid();

        return <<<HTML
        <style>
        .ek-cd-sec { background: linear-gradient(135deg, #0b2545 0%, #13294b 60%, #1c3a6e 100%); color: #ffffff; padding: 60px 24px; text-align: center; border-radius: 20px; max-width: 1140px; margin: 30px auto; position: relative; overflow: hidden; }
        .ek-cd-sec::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(199,154,59,.15) 0%, transparent 60%); pointer-events: none; }
        .ek-cd-kicker { display: inline-block; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #c79a3b; background: rgba(199,154,59,.15); border: 1px solid rgba(199,154,59,.3); padding: 6px 16px; border-radius: 999px; margin-bottom: 16px; }
        .ek-cd-title { font-size: 32px; font-weight: 800; margin: 0 0 32px; color: #ffffff; line-height: 1.25; }
        .ek-cd-boxes { display: flex; align-items: center; justify-content: center; gap: 16px; flex-wrap: wrap; margin-bottom: 36px; }
        .ek-cd-box { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(10px); min-width: 100px; padding: 18px 14px; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,.2); }
        .ek-cd-num { font-size: 42px; font-weight: 800; color: #ffffff; line-height: 1; font-family: ui-monospace, SFMono-Regular, monospace; }
        .ek-cd-lbl { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #c79a3b; margin-top: 6px; }
        .ek-cd-btn { display: inline-block; background: linear-gradient(135deg, #e0b94e, #c79a3b); color: #0b2545; font-size: 15px; font-weight: 700; padding: 14px 32px; border-radius: 999px; text-decoration: none; box-shadow: 0 10px 28px rgba(199,154,59,.4); transition: all .22s ease; }
        .ek-cd-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(199,154,59,.55); }
        @media(max-width: 600px) { .ek-cd-title { font-size: 24px; } .ek-cd-box { min-width: 75px; padding: 12px 8px; } .ek-cd-num { font-size: 28px; } }
        </style>

        <div class="ek-cd-sec">
            <span class="ek-cd-kicker">{$eyebrow}</span>
            <h2 class="ek-cd-title">{$heading}</h2>
            <div class="ek-cd-boxes" id="{$timerId}" data-target="{$targetDate}">
                <div class="ek-cd-box"><div class="ek-cd-num" id="{$timerId}-days">00</div><div class="ek-cd-lbl">Days</div></div>
                <div class="ek-cd-box"><div class="ek-cd-num" id="{$timerId}-hours">00</div><div class="ek-cd-lbl">Hours</div></div>
                <div class="ek-cd-box"><div class="ek-cd-num" id="{$timerId}-mins">00</div><div class="ek-cd-lbl">Minutes</div></div>
                <div class="ek-cd-box"><div class="ek-cd-num" id="{$timerId}-secs">00</div><div class="ek-cd-lbl">Seconds</div></div>
            </div>
            <a href="{$btnUrl}" class="ek-cd-btn">{$btnText}</a>
        </div>

        <script>
        (function() {
            var target = new Date("{$targetDate}").getTime();
            function update() {
                var now = new Date().getTime();
                var diff = Math.max(0, target - now);
                var days = Math.floor(diff / (1000 * 60 * 60 * 24));
                var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var secs = Math.floor((diff % (1000 * 60)) / 1000);
                var dEl = document.getElementById("{$timerId}-days");
                var hEl = document.getElementById("{$timerId}-hours");
                var mEl = document.getElementById("{$timerId}-mins");
                var sEl = document.getElementById("{$timerId}-secs");
                if (dEl) dEl.textContent = String(days).padStart(2, '0');
                if (hEl) hEl.textContent = String(hours).padStart(2, '0');
                if (mEl) mEl.textContent = String(mins).padStart(2, '0');
                if (sEl) sEl.textContent = String(secs).padStart(2, '0');
            }
            update();
            setInterval(update, 1000);
        })();
        </script>
        HTML;
    }
}
