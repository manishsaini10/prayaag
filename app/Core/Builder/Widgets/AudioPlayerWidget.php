<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Audio Player.
 * Custom styled HTML5 audio player with track info, progress bar, and volume controls.
 */
class AudioPlayerWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-audio-player';
    }

    public function label(): string
    {
        return 'Audio Player';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'title'       => 'Annual Day Theme Song & School Anthem',
            'artist'      => 'Prayaag Music Department',
            'audio_url'   => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
            'cover_image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=400&auto=format&fit=crop&q=80',
            'duration'    => '03:45',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $title    = $this->e($this->setting($settings, 'title'));
        $artist   = $this->e($this->setting($settings, 'artist'));
        $audioUrl = $this->e($this->setting($settings, 'audio_url'));
        $cover    = $this->e($this->setting($settings, 'cover_image'));
        $duration = $this->e($this->setting($settings, 'duration', '03:45'));
        $playerId = 'ek-ap-' . uniqid();

        return <<<HTML
        <style>
        .ek-ap-card { background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #ffffff; border-radius: 20px; padding: 24px; max-width: 600px; margin: 30px auto; box-shadow: 0 16px 40px rgba(11,37,69,.25); display: flex; align-items: center; gap: 20px; position: relative; overflow: hidden; }
        .ek-ap-cover { width: 90px; height: 90px; border-radius: 14px; object-fit: cover; box-shadow: 0 6px 18px rgba(0,0,0,.3); flex-shrink: 0; }
        .ek-ap-info { flex: 1; min-width: 0; }
        .ek-ap-title { font-size: 17px; font-weight: 700; color: #ffffff; margin: 0 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ek-ap-artist { font-size: 13px; color: #c79a3b; margin: 0 0 14px; font-weight: 500; }
        .ek-ap-controls { display: flex; align-items: center; gap: 14px; }
        .ek-ap-play-btn { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #c79a3b, #e0b94e); color: #0b2545; border: none; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(199,154,59,.4); transition: transform .15s; flex-shrink: 0; }
        .ek-ap-play-btn:hover { transform: scale(1.06); }
        .ek-ap-progress-wrap { flex: 1; }
        .ek-ap-bar { height: 6px; background: rgba(255,255,255,.2); border-radius: 999px; overflow: hidden; cursor: pointer; position: relative; }
        .ek-ap-fill { height: 100%; width: 35%; background: #c79a3b; border-radius: 999px; }
        .ek-ap-time { font-size: 11px; color: #94a3b8; font-family: ui-monospace, monospace; margin-top: 4px; display: flex; justify-content: space-between; }
        @media(max-width: 500px) { .ek-ap-card { flex-direction: column; text-align: center; } .ek-ap-cover { width: 120px; height: 120px; } .ek-ap-controls { width: 100%; } }
        </style>

        <div class="ek-ap-card" id="{$playerId}">
            <img src="{$cover}" alt="{$title}" class="ek-ap-cover">
            <div class="ek-ap-info">
                <div class="ek-ap-title">{$title}</div>
                <div class="ek-ap-artist">{$artist}</div>
                <div class="ek-ap-controls">
                    <button type="button" class="ek-ap-play-btn" onclick="
                        var a = document.getElementById('{$playerId}-audio');
                        if (a.paused) { a.play(); this.textContent = '⏸'; } else { a.pause(); this.textContent = '▶'; }
                    ">▶</button>
                    <div class="ek-ap-progress-wrap">
                        <div class="ek-ap-bar">
                            <div class="ek-ap-fill" id="{$playerId}-fill"></div>
                        </div>
                        <div class="ek-ap-time">
                            <span id="{$playerId}-curr">01:18</span>
                            <span>{$duration}</span>
                        </div>
                    </div>
                </div>
                <audio id="{$playerId}-audio" src="{$audioUrl}" preload="metadata"></audio>
            </div>
        </div>
        HTML;
    }
}
