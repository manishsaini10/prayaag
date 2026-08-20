<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Sports Page Widget — Sports Arena, Coaches, Achievements & Annual Sports Meet
 */
class SportsPageWidget extends AbstractWidget
{
    public function type(): string { return 'sports-page'; }
    public function label(): string { return 'Sports Arena & Achievements (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Sports Arena & Athletic Excellence',
            'hero_subtitle' => 'Prayaag International School believes that sporting excellence is as vital as academic achievement. Our world-class sports infrastructure, professional coaching staff, and competitive fixtures calendar groom champions — on field and in life.',
            'hero_bg'       => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Sports.webp',

            'sports' => [
                ['icon' => '🏏', 'name' => 'Cricket',    'desc' => 'Full-size cricket ground with BCCI-standard pitch, fielding area, and professional coaching by ex-district players.'],
                ['icon' => '⚽', 'name' => 'Football',   'desc' => 'FIFA-dimension natural grass football ground with proper goal posts, corner flags, and evening floodlights.'],
                ['icon' => '🏀', 'name' => 'Basketball', 'desc' => 'Outdoor and semi-covered basketball courts with adjustable hoops suitable for junior and senior age groups.'],
                ['icon' => '🏸', 'name' => 'Badminton',  'desc' => 'Multiple indoor badminton courts in a dedicated sports hall with PU flooring, proper lighting, and net systems.'],
                ['icon' => '🏊', 'name' => 'Swimming',   'desc' => 'Olympic-regulation temperature-controlled indoor swimming pool with separate lanes for competitive training and recreational swimming.'],
                ['icon' => '🏓', 'name' => 'Table Tennis', 'desc' => 'ITTF-approved table tennis tables in a dedicated TT room for training, inter-school fixtures, and recreational play.'],
                ['icon' => '🤸', 'name' => 'Gymnastics & Yoga', 'desc' => 'Matted gymnastics studio and a dedicated yoga hall for flexibility, mental wellness, and physical conditioning.'],
                ['icon' => '🏃', 'name' => '400m Running Track', 'desc' => 'Synthetic all-weather 6-lane running track hosting inter-school athletics meets, Annual Sports Day, and daily conditioning drills.'],
            ],

            'achievements' => [
                '🥇 State Level Cricket Championship — 2024 (U-17 Boys)',
                '🥇 Haryana State Swimming Championship — Gold Medal (2023)',
                '🥈 National School Games — Football (U-14, 2023)',
                '🏆 Panipat District Basketball Champions — 3 Consecutive Years',
                '🥇 CBSE National Athletics — 100m & 400m Sprint (2022)',
                '🎖️ Best Sports School — Panipat Education Excellence Awards 2024',
            ],

            'coaches' => [
                ['name' => 'Mr. Rajiv Sharma',   'sport' => 'Cricket & Athletics', 'exp' => '18 Years'],
                ['name' => 'Mr. Arun Malik',      'sport' => 'Football & Basketball', 'exp' => '12 Years'],
                ['name' => 'Ms. Priya Bhatia',    'sport' => 'Swimming & Gymnastics', 'exp' => '10 Years'],
                ['name' => 'Mr. Deepak Verma',    'sport' => 'Badminton & Table Tennis', 'exp' => '15 Years'],
            ],

            'sports_day_note' => 'Annual Sports Day is held every January — a grand carnival of athleticism, sportsmanship, and school spirit celebrating every student\'s potential.',
            'cta_url'   => '/admissions',
            'cta_label' => 'Enroll Your Champion',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'sports_day_note', 'label' => 'Sports Day Note', 'type' => 'textarea'],
            ['key' => 'cta_label',     'label' => 'CTA Button Text', 'type' => 'text'],
            ['key' => 'cta_url',       'label' => 'CTA Button URL',  'type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.sports-page', $settings)->render();
    }
}
