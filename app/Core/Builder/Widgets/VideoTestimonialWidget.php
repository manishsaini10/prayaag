<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Models\VideoTestimonial;

class VideoTestimonialWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'video_testimonial';
    }

    public function label(): string
    {
        return 'Video Testimonials';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultSettings(): array
    {
        return [
            'heading'           => 'What Parents & Students Say',
            'eyebrow'           => 'Video Testimonials',
            'layout_style'      => 'grid',         // grid | carousel | reel_slider | masonry | spotlight | wall_mosaic | story_bubble | spotlight_modal
            'card_style'        => 'shadow',       // minimal | shadow | glassmorphism | fullscreen_immersive | story_style
            'max_videos'        => 8,
            'group'             => '',             // Filter by specific group e.g. "Admissions", "Sports"
            'filter_tag_type'   => '',
            'filter_tag_value'  => '',
            'show_tabs'         => false,          // Interactive category filter tabs on front-end
            'show_cta'          => true,
            'autoplay'          => false,
            'muted'             => true,
            'carousel_style'    => 'default',
            'story_position'    => 'bottom-right',
            'section_bg'        => 'transparent',
            'card_bg'           => '#ffffff',
            'text_color'        => '#0f172a',
            'border_radius'     => '1rem',
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'layout_style'    => ['grid', 'carousel', 'reel_slider', 'masonry', 'spotlight', 'wall_mosaic', 'story_bubble', 'spotlight_modal'],
            'card_style'      => ['minimal', 'shadow', 'glassmorphism', 'fullscreen_immersive', 'story_style'],
            'filter_tag_type' => ['', 'program', 'event', 'class', 'department', 'custom'],
            'carousel_style'  => ['default', 'spotlight'],
            'story_position'  => ['bottom-right', 'bottom-left'],
            'max_videos'      => [4, 6, 8, 12, 16],
        ];
    }

    public function isDynamic(): bool
    {
        return true; // live queries — never statically cached
    }

    public function render(array $settings, array $context = []): string
    {
        $layout   = $this->setting($settings, 'layout_style', 'grid');
        $max      = (int) $this->setting($settings, 'max_videos', 8);
        $group    = $this->setting($settings, 'group', '');
        $tagType  = $this->setting($settings, 'filter_tag_type', '');
        $tagValue = $this->setting($settings, 'filter_tag_value', '');
        $showTabs = (bool) $this->setting($settings, 'show_tabs', false);

        if (! empty($group)) {
            $tagValue = $group;
        }

        // SAFETY GATE: only approved + consent-confirmed testimonials
        $videos = VideoTestimonial::approved()
            ->matchingTags($tagType ?: null, $tagValue ?: null)
            ->with('tags')
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order')
            ->limit($max)
            ->get();

        if ($videos->isEmpty()) {
            return '';
        }

        $availableGroups = $showTabs
            ? \App\Models\VideoTestimonialTag::distinct()->pluck('tag_value')->filter()->values()
            : collect();

        $view = match ($layout) {
            'carousel'       => 'widgets.video-testimonial.carousel',
            'reel_slider'    => 'widgets.video-testimonial.reel-slider',
            'masonry'        => 'widgets.video-testimonial.masonry',
            'spotlight'      => 'widgets.video-testimonial.spotlight',
            'wall_mosaic'    => 'widgets.video-testimonial.wall-mosaic',
            'story_bubble'   => 'widgets.video-testimonial.story-bubble',
            'spotlight_modal'=> 'widgets.video-testimonial.spotlight-modal',
            default          => 'widgets.video-testimonial.grid',
        };

        return view($view, [
            'videos'          => $videos,
            'settings'        => $settings,
            'heading'         => $this->setting($settings, 'heading', ''),
            'eyebrow'         => $this->setting($settings, 'eyebrow', ''),
            'showCta'         => (bool) $this->setting($settings, 'show_cta', true),
            'showTabs'        => $showTabs,
            'availableGroups' => $availableGroups,
            'autoplay'        => false, // ALWAYS false — spec requirement
            'muted'           => true,
            'carouselStyle'   => $this->setting($settings, 'carousel_style', 'default'),
            'storyPosition'   => $this->setting($settings, 'story_position', 'bottom-right'),
        ])->render();
    }
}
