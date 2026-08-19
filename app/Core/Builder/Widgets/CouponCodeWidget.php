<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Coupon Code / Scholarship Voucher Card.
 * Promotional discount voucher with click-to-copy code functionality.
 */
class CouponCodeWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-coupon-code';
    }

    public function label(): string
    {
        return 'Coupon Code Card';
    }

    public function category(): string
    {
        return 'pro-features';
    }

    public function defaultSettings(): array
    {
        return [
            'discount'   => '100% OFF',
            'title'      => 'Merit Scholarship Voucher (Class X & XII Toppers)',
            'desc'       => 'Lorem ipsum dolor sit amet, offering full tuition fee waiver for students scoring 95%+ in Board Examinations.',
            'code'       => 'MERIT2026',
            'valid_till' => '31st August 2026',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $discount  = $this->e($this->setting($settings, 'discount'));
        $title     = $this->e($this->setting($settings, 'title'));
        $desc      = $this->e($this->setting($settings, 'desc'));
        $code      = $this->e($this->setting($settings, 'code'));
        $validTill = $this->e($this->setting($settings, 'valid_till'));

        $cpId = 'ek-cp-' . uniqid();

        return <<<HTML
        <style>
        .ek-cp-card { background: #ffffff; border: 2px dashed #c79a3b; border-radius: 20px; padding: 28px; max-width: 580px; margin: 30px auto; display: flex; gap: 20px; align-items: center; box-shadow: 0 10px 30px rgba(199,154,59,.12); position: relative; }
        .ek-cp-badge-wrap { background: linear-gradient(135deg, #0b2545, #1c3a6e); color: #c79a3b; border-radius: 14px; padding: 18px 14px; text-align: center; flex-shrink: 0; min-width: 100px; }
        .ek-cp-badge-val { font-size: 22px; font-weight: 800; line-height: 1.1; }
        .ek-cp-badge-lbl { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; opacity: .85; margin-top: 4px; color: #ffffff; }
        .ek-cp-info { flex: 1; }
        .ek-cp-title { font-size: 17px; font-weight: 700; color: #0b2545; margin: 0 0 6px; }
        .ek-cp-desc { font-size: 13px; color: #64748b; margin: 0 0 14px; line-height: 1.5; }
        .ek-cp-box { display: flex; align-items: center; gap: 8px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; }
        .ek-cp-code { font-size: 15px; font-weight: 800; color: #0b2545; font-family: ui-monospace, monospace; flex: 1; letter-spacing: 1px; }
        .ek-cp-btn { background: #0b2545; color: #ffffff; border: none; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 6px; cursor: pointer; transition: background .2s; }
        .ek-cp-btn:hover { background: #c79a3b; color: #0b2545; }
        .ek-cp-valid { font-size: 11px; color: #94a3b8; margin-top: 8px; display: block; font-weight: 500; }
        @media(max-width: 500px) { .ek-cp-card { flex-direction: column; text-align: center; } }
        </style>

        <div class="ek-cp-card">
            <div class="ek-cp-badge-wrap">
                <div class="ek-cp-badge-val">{$discount}</div>
                <div class="ek-cp-badge-lbl">Scholarship</div>
            </div>
            <div class="ek-cp-info">
                <h3 class="ek-cp-title">{$title}</h3>
                <p class="ek-cp-desc">{$desc}</p>
                <div class="ek-cp-box">
                    <span class="ek-cp-code" id="{$cpId}-code">{$code}</span>
                    <button type="button" class="ek-cp-btn" onclick="
                        var code = document.getElementById('{$cpId}-code').textContent;
                        navigator.clipboard.writeText(code);
                        this.textContent = 'Copied! ✓';
                        setTimeout(() => this.textContent = 'Copy Code', 2000);
                    ">Copy Code</button>
                </div>
                <span class="ek-cp-valid">⏰ Valid till: {$validTill}</span>
            </div>
        </div>
        HTML;
    }
}
