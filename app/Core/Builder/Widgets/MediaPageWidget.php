<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page Media ("Life at Prayaag") Widget.
 * Renders complete campus life gallery — Dance & Music, Sports & Athletics,
 * Arts & Craft, Fun Activities, and Auto-playing Newspaper Press Clippings Slider with Lightbox.
 * Every gallery image, title, and slider clipping is 100% editable in Page Builder settings.
 */
class MediaPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'media-page';
    }

    public function label(): string
    {
        return 'Media Page (Life at Prayaag Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'hero_title'           => 'Life at Prayaag',
            'hero_subtitle'        => 'Explore life at Prayaag International School — performing arts studios, championship sports arenas, fine arts ateliers, early childhood play zones, and celebrated newspaper features.',
            
            // 1. Dance & Music Section
            'dance_music_title'    => 'Dance & Music',
            'dance_music_images'   => [
                '/images/media/Dance_class.jpg',
                '/images/media/student-playing-keyboard.webp',
                '/images/media/Teacher-teaching-keyboard.webp',
            ],

            // 2. Sports Section
            'sports_title'         => 'Sports',
            'sports_images'        => [
                '/images/media/Football.jpg',
                '/images/media/Shooting.jpg',
                '/images/media/Basket.jpg',
            ],

            // 3. Arts & Craft Section
            'arts_craft_title'     => 'Arts & Craft',
            'arts_craft_images'    => [
                '/images/media/Painting-practice-prayaag-student.webp',
                '/images/media/Painting-at-Prayaag-International-School.webp',
                '/images/media/Prayaag-International-School-Laibrary.webp',
            ],

            // 4. Fun Activities Section
            'fun_activities_title' => 'Fun Activities',
            'fun_activities_images'=> [
                '/images/media/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp',
                '/images/media/Junior-children-playing.webp',
                '/images/media/Children-playing-at-swimimg-pool.webp',
            ],

            // 5. News Clippings Slider Section
            'news_title'           => 'News',
            'news_images'          => [
                '/images/media/WhatsApp-Image-2025-08-21-at-10.50.47-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-09-30-at-10.16.22-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-10-08-at-2.28.58-PM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-10-09-at-9.41.27-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-10-18-at-8.45.26-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-11-10-at-2.24.58-PM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-11-11-at-4.53.30-PM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-11-16-at-10.00.53-AM_1350x1350.jpg',
                '/images/media/News-5.jpg',
                '/images/media/WhatsApp-Image-2026-01-19-at-12.54.27-PM-1.jpeg',
                '/images/media/WhatsApp-Image-2025-09-30-at-10.16.21-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-09-30-at-10.16.19-AM_1350x1350.jpg',
                '/images/media/News-6.jpg',
                '/images/media/News-4.jpg',
                '/images/media/News-2.jpg',
                '/images/media/News-1.jpg',
                '/images/media/news-123.jpeg',
            ],

            // Slider Behavior Settings
            'autoplay'             => true,
            'interval'             => 3000,
            'animation_speed'      => 600,
            'pause_on_hover'       => true,
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $mergedSettings = array_merge($this->defaultSettings(), $settings);

        return view('widgets.media-page', [
            'settings' => $mergedSettings,
        ])->render();
    }
}
