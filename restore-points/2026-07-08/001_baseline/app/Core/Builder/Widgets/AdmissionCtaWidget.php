<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-bleed admission call-to-action band. Defaults carry the existing
 * home-page admission CTA.
 */
class AdmissionCtaWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'admission-cta';
    }

    public function label(): string
    {
        return 'Admission CTA';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'heading'      => 'Admission Open 2026-27',
            'text'         => 'Give your child the Prayaag advantage. Register online today.',
            'button_label' => 'Online Registration →',
            'button_url'   => 'https://pisp.accevate.com/registration/',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $heading = $this->e($this->setting($settings, 'heading'));
        $text    = $this->e($this->setting($settings, 'text'));

        $button = '';
        if ($label = $this->setting($settings, 'button_label')) {
            $button = '<a class="btn btn-gold" href="' . $this->e($this->setting($settings, 'button_url', '#')) . '">' . $this->e($label) . '</a>';
        }

        return <<<HTML
        <div class="fullbleed"><section class="admit-cta"><div class="container" data-reveal>
            <h2>{$heading}</h2>
            <p>{$text}</p>
            {$button}
        </div></section></div>
        HTML;
    }
}
