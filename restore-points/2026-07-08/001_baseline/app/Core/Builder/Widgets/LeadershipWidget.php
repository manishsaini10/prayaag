<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Leadership message cards (Director / Principal). Defaults carry the existing
 * home-page messages verbatim; each person has a name, role and paragraphs.
 */
class LeadershipWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'leadership';
    }

    public function label(): string
    {
        return 'Leadership Messages';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Welcome to Prayaag',
            'heading' => 'A Message from Our Leadership',
            'sub'     => 'Nurturing young minds and shaping the leaders of tomorrow through a shared commitment between dedicated teachers, motivated students and supportive parents.',
            'people'  => [
                [
                    'name' => 'Mrs. Anju Gupta',
                    'role' => 'Director',
                    'photo' => '/storage/media/imported/01KTQWW0KDMYKAGQS1XNT1R7V7.webp',
                    'body' => [
                        'As we continue our journey to nurture young minds and shape the leaders of tomorrow, we are delighted to connect with you and share our vision. At Prayaag International School, we believe education is a shared commitment between dedicated teachers, motivated students and supportive parents.',
                        'Our mission is to provide a dynamic and nurturing environment where every child can discover their unique potential and develop into a confident, responsible and well-rounded individual — blending academic excellence with creativity, critical thinking and a strong sense of community.',
                        'Thank you for entrusting us with your child’s education. We look forward to a successful and fulfilling academic year ahead.',
                    ],
                ],
                [
                    'name' => 'Mrs. Mamta Sachdeva',
                    'role' => 'Principal',
                    'photo' => '/storage/media/imported/01KTQWW217PTPXXT14ATFA7F48.webp',
                    'body' => [
                        'At Prayaag International School, education is treated as a serious trust, not a transaction. The school is committed to deep, disciplined learning rooted in Indian cultural values while equipping students with essential 21st-century competencies.',
                        'In a world shaped by artificial intelligence, we adopt a measured and vigilant approach — guiding students to treat AI as an aid, not a substitute, to uphold academic honesty, and to think, write and solve independently in a technology-rich environment.',
                        'With a purposeful partnership with parents, we aim to stand apart as an institution defined by preparing resilient, capable individuals who can help run and renew an ever-changing world.',
                    ],
                ],
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

        $people = $this->setting($settings, 'people', []);
        if (! is_array($people)) {
            $people = [];
        }

        $cards = '';
        $i = 0;
        foreach ($people as $person) {
            $name = $this->e($person['name'] ?? '');
            $role = $this->e($person['role'] ?? '');
            $initials = $this->e($this->initials($person['name'] ?? ''));
            $photo = trim((string) ($person['photo'] ?? ''));
            $paras = '';
            foreach ((array) ($person['body'] ?? []) as $p) {
                $paras .= '<p>' . $this->e($p) . '</p>';
            }
            $delay = ($i % 6) + 1;
            $i++;
            $cards .= '<div class="lead-card" data-reveal data-reveal-delay="' . $delay . '">'
                . '<div class="lead-top' . ($photo !== '' ? ' has-photo' : '') . '">'
                . ($photo !== '' ? '<img class="lead-top-img" src="' . $this->e($photo) . '" alt="' . $name . '">' : '')
                . '<div class="lead-top-text"><h4>' . $name . '</h4><small>' . $role . '</small></div></div>'
                . '<div class="lead-body">' . $paras . '</div></div>';
        }

        return $head . '<div class="lead-grid">' . $cards . '</div>';
    }
}
