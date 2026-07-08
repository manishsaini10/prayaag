<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-bleed hero with kicker, headline, tagline and two CTAs over a cover
 * image. Content is editable; defaults carry the existing home-page hero.
 */
class HeroWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'hero';
    }

    public function label(): string
    {
        return 'Hero';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'kicker'          => '★ Admission Open 2026-27',
            'heading'         => 'Prayaag International School, Panipat',
            'tagline'         => 'Life begins here…',
            'primary_label'   => 'Online Registration →',
            'primary_url'     => 'https://pisp.accevate.com/registration/',
            'secondary_label' => 'Discover the School',
            'secondary_url'   => '/about-us/',
            'image'           => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp',
            'slides'          => [],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $kicker  = $this->e($this->setting($settings, 'kicker'));
        $heading = $this->e($this->setting($settings, 'heading'));
        $tagline = $this->e($this->setting($settings, 'tagline'));

        // First the primary image, then any additional slide images. Each
        // becomes a stacked, crossfading layer (site.js auto-rotates >1).
        $images = [];
        if ($img = $this->setting($settings, 'image')) {
            $images[] = $img;
        }
        foreach ((array) $this->setting($settings, 'slides', []) as $slide) {
            $url = is_array($slide) ? ($slide['image'] ?? '') : (string) $slide;
            if ($url !== '') {
                $images[] = $url;
            }
        }

        $slides = '';
        foreach ($images as $idx => $url) {
            $cls = $idx === 0 ? 'hero-slide is-active' : 'hero-slide';
            $slides .= '<div class="' . $cls . '" style="background-image:url(\'' . $this->e($url) . '\')"></div>';
        }
        $slidesLayer = $slides ? '<div class="hero-slides">' . $slides . '</div>' : '';

        $cta = '';
        if ($pl = $this->setting($settings, 'primary_label')) {
            $cta .= '<a class="btn btn-gold" href="' . $this->e($this->setting($settings, 'primary_url', '#')) . '">' . $this->e($pl) . '</a>';
        }
        if ($sl = $this->setting($settings, 'secondary_label')) {
            $cta .= '<a class="btn btn-ghost" href="' . $this->e($this->setting($settings, 'secondary_url', '#')) . '">' . $this->e($sl) . '</a>';
        }

        $kickerHtml = $kicker ? '<span class="hero-kicker" data-reveal>' . $kicker . '</span>' : '';

        return <<<HTML
        <div class="fullbleed"><section class="hero">{$slidesLayer}<div class="container">
            {$kickerHtml}
            <h1 data-reveal data-reveal-delay="1">{$heading}</h1>
            <p class="hero-tag" data-reveal data-reveal-delay="2">{$tagline}</p>
            <div class="hero-cta" data-reveal data-reveal-delay="3">{$cta}</div>
        </div></section></div>
        HTML;
    }
}
