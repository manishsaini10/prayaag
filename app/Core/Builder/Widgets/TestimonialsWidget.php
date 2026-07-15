<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Models\Testimonial;

/**
 * Dynamic widget: published testimonials, rendered in one of several layouts.
 * The data comes from the Testimonials module; the look is chosen via the
 * "layout" setting (a dropdown in the Page Builder).
 */
class TestimonialsWidget extends AbstractWidget
{
    /** Available design variants (value => label). */
    public const LAYOUTS = [
        'cards'   => 'Cards (3 columns)',
        'grid2'   => 'Cards (2 columns)',
        'compact' => 'Compact grid',
        'list'    => 'List (rows)',
        'minimal' => 'Minimal (centered)',
        'masonry' => 'Masonry',
        'slider'  => 'Slider (swipe)',
        'bubble'  => 'Speech bubbles',
    ];

    public function type(): string
    {
        return 'testimonials';
    }

    public function label(): string
    {
        return 'Testimonials';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return [
            'layout'  => 'cards',
            'limit'   => 6,
            'eyebrow' => '',
            'heading' => '',
            'sub'     => '',
        ];
    }

    /** Expose dropdown options for the editor (key => allowed values). */
    public function fieldOptions(): array
    {
        return ['layout' => array_keys(self::LAYOUTS)];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $layout = (string) $this->setting($settings, 'layout', 'cards');
        if (! isset(self::LAYOUTS[$layout])) {
            $layout = 'cards';
        }
        $limit = max(1, (int) $this->setting($settings, 'limit', 6));

        $items = Testimonial::published()->orderBy('sort_order')->limit($limit)->get();

        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        if ($items->isEmpty()) {
            return $head . '<div class="tw tw--' . $layout . ' pb-empty"></div>';
        }

        $cards = '';
        $i = 0;
        foreach ($items as $t) {
            $author = (string) ($t->author ?? '');
            $role   = (string) ($t->role ?? '');
            $quote  = (string) ($t->quote ?? '');
            $rating = max(0, min(5, (int) ($t->rating ?? 0)));

            $stars = '';
            if ($rating > 0) {
                $stars = '<div class="tw-stars">' . str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) . '</div>';
            }

            $meta = '<b>' . $this->e($author) . '</b>';
            if ($role !== '') {
                $meta .= '<small>' . $this->e($role) . '</small>';
            }

            // Image / Avatar rendering
            $image = $t->image;
            if ($image) {
                $avHtml = '<img src="' . asset($image) . '" class="tw-av" style="object-fit:cover">';
            } else {
                $avHtml = '<span class="tw-av">' . $this->e($this->initials($author)) . '</span>';
            }

            // Slider items scroll horizontally (off-screen right), so a scroll
            // reveal would leave them invisible — skip data-reveal for slider.
            $reveal = $layout === 'slider' ? '' : ' data-reveal data-reveal-delay="' . (($i % 3) + 1) . '"';
            $cards .= '<figure class="tw-item"' . $reveal . '>'
                . $stars
                . '<blockquote class="tw-quote">' . $this->e($quote) . '</blockquote>'
                . '<figcaption class="tw-by">'
                . $avHtml
                . '<span class="tw-meta">' . $meta . '</span>'
                . '</figcaption></figure>';
            $i++;
        }

        return $head . '<div class="tw tw--' . $layout . '">' . $cards . '</div>';
    }
}
