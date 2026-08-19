<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — WhatsApp Live Chat Widget.
 * Interactive WhatsApp click-to-talk widget with agent status and preset greeting.
 */
class WhatsAppChatWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-whatsapp';
    }

    public function label(): string
    {
        return 'WhatsApp Chat';
    }

    public function category(): string
    {
        return 'pro-general';
    }

    public function defaultSettings(): array
    {
        return [
            'name'       => 'Admissions Helpdesk',
            'status'     => 'Online • Responds instantly',
            'phone'      => '919999999999',
            'avatar'     => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&auto=format&fit=crop&q=80',
            'greeting'   => 'Hello! 👋 Welcome to Prayaag International School. How can we help you with admissions today?',
            'btn_label'  => 'Start Chat on WhatsApp',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $name     = $this->e($this->setting($settings, 'name'));
        $status   = $this->e($this->setting($settings, 'status'));
        $phone    = preg_replace('/[^0-9]/', '', (string) $this->setting($settings, 'phone', '919999999999'));
        $avatar   = $this->e($this->setting($settings, 'avatar'));
        $greeting = $this->e($this->setting($settings, 'greeting'));
        $btnLabel = $this->e($this->setting($settings, 'btn_label', 'Start Chat on WhatsApp'));

        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($greeting);

        return <<<HTML
        <style>
        .ek-wa-card { background: #ffffff; border: 1.5px solid #25d366; border-radius: 20px; max-width: 420px; margin: 30px auto; overflow: hidden; box-shadow: 0 16px 40px rgba(37,211,102,.15); font-family: system-ui, sans-serif; }
        .ek-wa-header { background: #075e54; color: #ffffff; padding: 20px; display: flex; align-items: center; gap: 14px; }
        .ek-wa-avatar-wrap { position: relative; }
        .ek-wa-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; }
        .ek-wa-dot { position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; background: #25d366; border: 2px solid #ffffff; border-radius: 50%; }
        .ek-wa-name { font-size: 16px; font-weight: 700; color: #ffffff; margin: 0 0 2px; }
        .ek-wa-status { font-size: 12px; color: rgba(255,255,255,.85); }
        .ek-wa-body { padding: 20px; background: #efeae2; min-height: 120px; }
        .ek-wa-bubble { background: #ffffff; color: #111b21; padding: 12px 16px; border-radius: 0 16px 16px 16px; font-size: 13.5px; line-height: 1.5; box-shadow: 0 2px 6px rgba(0,0,0,.08); position: relative; }
        .ek-wa-footer { padding: 16px 20px; background: #ffffff; border-top: 1px solid #e2e8f0; text-align: center; }
        .ek-wa-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #25d366; color: #ffffff; font-size: 14px; font-weight: 700; padding: 12px; border-radius: 10px; text-decoration: none; box-shadow: 0 6px 18px rgba(37,211,102,.3); transition: background .2s; }
        .ek-wa-btn:hover { background: #128c7e; }
        </style>

        <div class="ek-wa-card">
            <div class="ek-wa-header">
                <div class="ek-wa-avatar-wrap">
                    <img src="{$avatar}" alt="{$name}" class="ek-wa-avatar">
                    <span class="ek-wa-dot"></span>
                </div>
                <div>
                    <h3 class="ek-wa-name">{$name}</h3>
                    <div class="ek-wa-status">{$status}</div>
                </div>
            </div>
            <div class="ek-wa-body">
                <div class="ek-wa-bubble">{$greeting}</div>
            </div>
            <div class="ek-wa-footer">
                <a href="{$waUrl}" target="_blank" rel="noopener" class="ek-wa-btn">
                    <span>💬</span> {$btnLabel}
                </a>
            </div>
        </div>
        HTML;
    }
}
