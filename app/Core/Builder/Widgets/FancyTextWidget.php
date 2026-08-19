<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Fancy Animated Text.
 * Dynamic typing animation effect on headline keywords.
 */
class FancyTextWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-fancy-text';
    }

    public function label(): string
    {
        return 'Fancy Animated Text';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'prefix' => 'We Empower Students to Become',
            'words'  => ['Global Leaders', 'Innovators', 'Scholars', 'Champions'],
            'suffix' => 'of Tomorrow.',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $prefix = $this->e($this->setting($settings, 'prefix'));
        $words  = (array) $this->setting($settings, 'words', ['Global Leaders', 'Innovators', 'Scholars']);
        $suffix = $this->e($this->setting($settings, 'suffix'));

        $wordsJson = json_encode(array_map([$this, 'e'], $words));
        $ftId = 'ek-ft-' . uniqid();

        return <<<HTML
        <style>
        .ek-ft-sec { text-align: center; padding: 40px 20px; max-width: 900px; margin: 20px auto; }
        .ek-ft-heading { font-size: 36px; font-weight: 800; color: #0b2545; line-height: 1.3; }
        .ek-ft-dynamic { color: #c79a3b; border-bottom: 3px solid #c79a3b; padding-bottom: 2px; display: inline-block; position: relative; min-width: 140px; text-align: left; }
        .ek-ft-cursor { display: inline-block; width: 2px; height: 1em; background: #c79a3b; margin-left: 2px; animation: ek-blink .7s infinite; vertical-align: middle; }
        @keyframes ek-blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        @media(max-width: 600px) { .ek-ft-heading { font-size: 26px; } }
        </style>

        <div class="ek-ft-sec">
            <h2 class="ek-ft-heading">
                {$prefix}
                <span class="ek-ft-dynamic" id="{$ftId}-word">{$words[0]}</span><span class="ek-ft-cursor"></span>
                {$suffix}
            </h2>
        </div>

        <script>
        (function() {
            var words = {$wordsJson};
            var el = document.getElementById("{$ftId}-word");
            if (!el || !words.length) return;
            var idx = 0;
            setInterval(function() {
                idx = (idx + 1) % words.length;
                el.style.opacity = '0';
                setTimeout(function() {
                    el.textContent = words[idx];
                    el.style.opacity = '1';
                }, 200);
            }, 2500);
        })();
        </script>
        HTML;
    }
}
