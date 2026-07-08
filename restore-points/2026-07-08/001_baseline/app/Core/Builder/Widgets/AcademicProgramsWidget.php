<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/** Academic program / stage cards. */
class AcademicProgramsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'academic-programs';
    }

    public function label(): string
    {
        return 'Academic Programs';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Academics',
            'heading' => 'Programs for Every Stage',
            'sub'     => 'A continuous, well-structured journey from early years to senior school.',
            'items'   => [
                ['age' => 'Nursery – KG', 'title' => 'Foundational Years', 'text' => 'Play-based, activity-led learning that nurtures curiosity, language and motor skills in a warm, safe environment.'],
                ['age' => 'Class I – VIII', 'title' => 'Preparatory & Middle', 'text' => 'A strong CBSE foundation across languages, mathematics, science and the arts, with a focus on conceptual clarity.'],
                ['age' => 'Class IX – XII', 'title' => 'Secondary & Senior', 'text' => 'Rigorous board preparation, streams in Science/Commerce/Humanities, and dedicated career guidance.'],
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

        $cards = '';
        $i = 0;
        foreach ((array) $this->setting($settings, 'items', []) as $item) {
            $age   = $this->e($item['age'] ?? '');
            $title = $this->e($item['title'] ?? '');
            $text  = $this->e($item['text'] ?? '');
            $cards .= '<div class="prog" data-reveal data-reveal-delay="' . (($i % 3) + 1) . '">'
                . '<div class="top"><div class="age">' . $age . '</div><h4>' . $title . '</h4></div>'
                . '<div class="body">' . $text . '</div></div>';
            $i++;
        }

        return $head . '<div class="prog-grid">' . $cards . '</div>';
    }
}
