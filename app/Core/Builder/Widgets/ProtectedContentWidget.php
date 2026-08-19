<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Protected Content Box.
 * Locked content container requiring passcode validation to reveal sensitive documents/links.
 */
class ProtectedContentWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-protected-content';
    }

    public function label(): string
    {
        return 'Protected Content';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'title'          => 'Confidential Syllabus & Mock Test Answer Key (2026)',
            'passcode'       => 'PRAYAAG123',
            'locked_msg'     => 'This section is restricted to enrolled students & parents. Please enter your passcode to view.',
            'secret_heading' => '🔓 Unlocked Student & Parent Portal Access',
            'secret_content' => 'Lorem ipsum dolor sit amet, download official mid-term syllabus PDFs, monthly test schedules, and previous year answer keys.',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $title     = $this->e($this->setting($settings, 'title'));
        $passcode  = $this->e($this->setting($settings, 'passcode', 'PRAYAAG123'));
        $lockedMsg = $this->e($this->setting($settings, 'locked_msg'));
        $secHead   = $this->e($this->setting($settings, 'secret_heading'));
        $secCont   = $this->e($this->setting($settings, 'secret_content'));

        $boxId = 'ek-pc-' . uniqid();

        return <<<HTML
        <style>
        .ek-pc-card { background: #ffffff; border: 1.5px dashed #cbd5e1; border-radius: 20px; padding: 36px; max-width: 700px; margin: 30px auto; text-align: center; box-shadow: 0 10px 30px rgba(11,37,69,.05); }
        .ek-pc-lock-icon { font-size: 44px; margin-bottom: 12px; }
        .ek-pc-title { font-size: 20px; font-weight: 800; color: #0b2545; margin: 0 0 8px; }
        .ek-pc-msg { font-size: 14px; color: #64748b; margin: 0 0 24px; line-height: 1.5; }
        .ek-pc-form { display: flex; gap: 10px; justify-content: center; max-width: 380px; margin: 0 auto; }
        .ek-pc-input { padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; flex: 1; text-align: center; letter-spacing: 1px; }
        .ek-pc-btn { background: #0b2545; color: #ffffff; border: none; font-size: 14px; font-weight: 700; padding: 10px 20px; border-radius: 10px; cursor: pointer; transition: background .2s; }
        .ek-pc-btn:hover { background: #c79a3b; color: #0b2545; }
        .ek-pc-secret { display: none; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 16px; padding: 24px; text-align: left; animation: ek-pc-fade .3s ease; }
        .ek-pc-secret.unlocked { display: block; }
        .ek-pc-sec-head { font-size: 18px; font-weight: 800; color: #166534; margin: 0 0 8px; }
        .ek-pc-sec-cont { font-size: 14px; color: #15803d; margin: 0; line-height: 1.6; }
        @keyframes ek-pc-fade { from { opacity: 0; transform: scale(.98); } to { opacity: 1; transform: scale(1); } }
        </style>

        <div class="ek-pc-card" id="{$boxId}">
            <div id="{$boxId}-locked">
                <div class="ek-pc-lock-icon">🔒</div>
                <h3 class="ek-pc-title">{$title}</h3>
                <p class="ek-pc-msg">{$lockedMsg}</p>
                <form class="ek-pc-form" onsubmit="
                    event.preventDefault();
                    var inp = document.getElementById('{$boxId}-inp').value;
                    if (inp === '{$passcode}') {
                        document.getElementById('{$boxId}-locked').style.display = 'none';
                        document.getElementById('{$boxId}-secret').classList.add('unlocked');
                    } else {
                        alert('Incorrect passcode. Please try again.');
                    }
                ">
                    <input type="password" class="ek-pc-input" id="{$boxId}-inp" placeholder="Enter Passcode..." required>
                    <button type="submit" class="ek-pc-btn">Unlock</button>
                </form>
            </div>
            <div class="ek-pc-secret" id="{$boxId}-secret">
                <h3 class="ek-pc-sec-head">{$secHead}</h3>
                <p class="ek-pc-sec-cont">{$secCont}</p>
            </div>
        </div>
        HTML;
    }
}
