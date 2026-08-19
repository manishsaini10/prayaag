<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Social Share Buttons.
 * One-click social sharing buttons for blog posts, news & announcements.
 */
class SocialShareWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-social-share';
    }

    public function label(): string
    {
        return 'Social Share Buttons';
    }

    public function category(): string
    {
        return 'pro-social';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Share this Page with Friends & Parents:',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $title = $this->e($this->setting($settings, 'title'));

        return <<<HTML
        <style>
        .pro-ss-box { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 20px 24px; max-width: 700px; margin: 24px auto; display: flex; align-items: center; justify-content: space-between; gap: 16px; box-shadow: 0 6px 20px rgba(11,37,69,.05); flex-wrap: wrap; }
        .pro-ss-title { font-size: 14px; font-weight: 700; color: #0b2545; margin: 0; }
        .pro-ss-btns { display: flex; gap: 10px; }
        .pro-ss-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 700; color: #ffffff; text-decoration: none; transition: transform .2s; }
        .pro-ss-btn:hover { transform: translateY(-2px); }
        .pro-ss-fb { background: #1877f2; }
        .pro-ss-wa { background: #25d366; }
        .pro-ss-tw { background: #000000; }
        .pro-ss-li { background: #0a66c2; }
        </style>

        <div class="pro-ss-box">
            <h4 class="pro-ss-title">{$title}</h4>
            <div class="pro-ss-btns">
                <a href="https://facebook.com" target="_blank" class="pro-ss-btn pro-ss-fb">📘 Share</a>
                <a href="https://wa.me" target="_blank" class="pro-ss-btn pro-ss-wa">💬 WhatsApp</a>
                <a href="https://x.com" target="_blank" class="pro-ss-btn pro-ss-tw">🐦 Tweet</a>
                <a href="https://linkedin.com" target="_blank" class="pro-ss-btn pro-ss-li">💼 Post</a>
            </div>
        </div>
        HTML;
    }
}
