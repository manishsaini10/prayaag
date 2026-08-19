<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Drop Caps Paragraph.
 * Editorial article callout block with prominent initial capital letter.
 */
class DropCapsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-drop-caps';
    }

    public function label(): string
    {
        return 'Drop Caps Paragraph';
    }

    public function category(): string
    {
        return 'pro-creative';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Message from the Principal',
            'heading' => 'Nurturing Academic & Moral Excellence',
            'text'    => 'At Prayaag International School, we strive to empower students with both modern scientific knowledge and timeless human values. Education is not merely the accumulation of facts, but the training of the mind to think independently, act compassionately, and lead courageously.',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $text = $this->e($this->setting($settings, 'text'));
        $firstChar = mb_substr($text, 0, 1);
        $restText  = mb_substr($text, 1);

        return <<<HTML
        <style>
        .pro-dc-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-left: 6px solid #c79a3b; border-radius: 16px; padding: 36px; max-width: 850px; margin: 30px auto; box-shadow: 0 10px 30px rgba(11,37,69,.06); }
        .pro-dc-para { font-size: 16px; color: #334155; line-height: 1.8; margin: 0; }
        .pro-dc-char { float: left; font-size: 58px; font-weight: 900; line-height: .85; color: #0b2545; background: linear-gradient(135deg, #0b2545, #c79a3b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-right: 12px; margin-bottom: 4px; font-family: 'Playfair Display', Georgia, serif; }
        </style>

        <section class="pro-dc-sec">
            {$head}
            <div class="pro-dc-card">
                <p class="pro-dc-para">
                    <span class="pro-dc-char">{$firstChar}</span>{$restText}
                </p>
            </div>
        </section>
        HTML;
    }
}
