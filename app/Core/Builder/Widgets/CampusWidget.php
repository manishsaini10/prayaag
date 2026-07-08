<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Two-wing campus showcase (Junior / Senior). Defaults carry the existing
 * home-page campus cards.
 */
class CampusWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'campus';
    }

    public function label(): string
    {
        return 'Campus Wings';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        $img = 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp';

        return [
            'eyebrow' => 'Our Campus',
            'heading' => 'The Place Where Beginners Become the Greatest',
            'sub'     => 'Two dedicated wings designed for every stage of a child’s growth.',
            'wings'   => [
                ['title' => 'Junior Wing', 'text' => 'A joyful, safe and stimulating world where curiosity is sparked and the foundations of learning are laid.', 'label' => 'Explore Junior Wing →', 'url' => '/junior-wing-school-in-panipat/', 'image' => $img],
                ['title' => 'Senior Wing', 'text' => 'An environment of academic rigour, leadership and character that prepares students for the world ahead.', 'label' => 'Explore Senior Wing →', 'url' => '/senior-wing-school-in-panipat/', 'image' => $img],
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
        foreach ((array) $this->setting($settings, 'wings', []) as $w) {
            $title = $this->e($w['title'] ?? '');
            $text  = $this->e($w['text'] ?? '');
            $label = $this->e($w['label'] ?? '');
            $url   = $this->e($w['url'] ?? '#');
            $img   = $w['image'] ?? '';
            $style = $img ? ' style="background-image:linear-gradient(180deg,rgba(14,47,94,.15),rgba(10,32,64,.5)),url(\'' . $this->e($img) . '\')"' : '';
            $delay = ($i % 2) + 1;
            $i++;
            $btn = $label ? '<a class="btn btn-gold" href="' . $url . '">' . $label . '</a>' : '';
            $cards .= '<div class="wing"' . $style . ' data-reveal data-reveal-delay="' . $delay . '">'
                . '<div class="inner"><h3>' . $title . '</h3><p>' . $text . '</p>' . $btn . '</div></div>';
        }

        return $head . '<div class="campus-grid">' . $cards . '</div>';
    }
}
