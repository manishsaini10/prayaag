<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\AcademicCalendarEntry;
use App\Models\Download;
use App\Models\Gallery;
use App\Models\Slider;
use App\Models\Subscriber;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Demo data for the remaining content modules so every widget has something to
 * render after a single migrate:fresh --seed.
 */
class Phase13Seeder extends Seeder
{
    public function run(): void
    {
        // Downloads
        foreach ([
            ['Prospectus 2026', 'Admissions', 'prospectus-2026.pdf', 'pdf'],
            ['Fee Structure', 'Admissions', 'fee-structure.pdf', 'pdf'],
            ['Transport Routes', 'General', 'transport-routes.pdf', 'pdf'],
        ] as $i => [$title, $cat, $file, $type]) {
            Download::firstOrCreate(
                ['title' => $title],
                ['category' => $cat, 'file' => '/storage/downloads/' . $file, 'file_type' => $type, 'sort_order' => $i]
            );
        }

        // Testimonials
        foreach ([
            ['Aarti Sharma', 'Parent', 'The teachers genuinely care about every child.'],
            ['Rohan Mehta', 'Alumnus, Class of 2018', 'The values I learned here shaped my career.'],
        ] as $i => [$author, $role, $quote]) {
            Testimonial::firstOrCreate(
                ['author' => $author],
                ['role' => $role, 'quote' => $quote, 'sort_order' => $i]
            );
        }

        // Achievements
        foreach ([
            ['State Science Olympiad — Gold', 2025],
            ['Best School Award (Regional)', 2024],
            ['National Football Runners-up', 2023],
        ] as $i => [$title, $year]) {
            Achievement::firstOrCreate(
                ['title' => $title],
                ['year' => $year, 'sort_order' => $i]
            );
        }

        // Gallery + images
        $gallery = Gallery::firstOrCreate(
            ['slug' => 'campus-life'],
            ['title' => 'Campus Life']
        );
        if (! $gallery->images()->exists()) {
            for ($i = 1; $i <= 4; $i++) {
                $gallery->images()->create([
                    'image'      => 'https://placehold.co/600x400?text=Campus+' . $i,
                    'caption'    => 'Campus photo ' . $i,
                    'sort_order' => $i,
                ]);
            }
        }

        // Slider + slides
        $slider = Slider::firstOrCreate(
            ['title' => 'Homepage Hero'],
            ['location' => 'homepage']
        );
        if (! $slider->slides()->exists()) {
            $slider->slides()->create([
                'image' => 'https://placehold.co/1200x500?text=Welcome', 'heading' => 'Welcome to Demo School',
                'subheading' => 'Nurturing curious minds since 1985', 'link_url' => '/contact',
                'link_label' => 'Get in touch', 'sort_order' => 0,
            ]);
            $slider->slides()->create([
                'image' => 'https://placehold.co/1200x500?text=Admissions+Open', 'heading' => 'Admissions Open',
                'subheading' => 'Join us for the 2026 session', 'link_url' => '/contact',
                'link_label' => 'Apply now', 'sort_order' => 1,
            ]);
        }

        // Academic calendar (incl. holidays)
        foreach ([
            ['Summer Break', 'holiday', '2026-05-15', '2026-06-15'],
            ['Term 1 Begins', 'term', '2026-04-01', null],
            ['Mid-term Exams', 'exam', '2026-07-20', '2026-07-28'],
        ] as [$title, $type, $start, $end]) {
            AcademicCalendarEntry::firstOrCreate(
                ['title' => $title],
                ['type' => $type, 'starts_on' => $start, 'ends_on' => $end]
            );
        }

        // Subscribers
        foreach (['parent1@example.test', 'parent2@example.test'] as $email) {
            Subscriber::firstOrCreate(
                ['email' => $email],
                ['status' => 'subscribed', 'subscribed_at' => now()]
            );
        }
    }
}
