<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/** Numbered admission-process steps. */
class AdmissionProcessWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'admission-process';
    }

    public function label(): string
    {
        return 'Admission Process (steps)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Admissions',
            'heading' => 'A Simple Admission Process',
            'sub'     => 'Four easy steps to join the Prayaag family.',
            'steps'   => [
                ['title' => 'Enquiry', 'text' => 'Submit an online enquiry or visit the campus to learn more.'],
                ['title' => 'Application', 'text' => 'Fill out the registration form and submit the required documents.'],
                ['title' => 'Interaction', 'text' => 'A friendly interaction with the child and parents.'],
                ['title' => 'Confirmation', 'text' => 'Complete the admission formalities and secure the seat.'],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $steps = '';
        $n = 1;
        foreach ((array) $this->setting($settings, 'steps', []) as $step) {
            $title = $this->e($step['title'] ?? '');
            $text  = $this->e($step['text'] ?? '');
            $steps .= '<div class="step" data-reveal data-reveal-delay="' . (($n - 1) % 4 + 1) . '">'
                . '<div class="num">' . $n . '</div><h4>' . $title . '</h4><p>' . $text . '</p></div>';
            $n++;
        }

        return $head . '<div class="steps">' . $steps . '</div>';
    }
}
