<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seeds 10 sample testimonials for testing the Testimonials widget layouts.
 * Idempotent: clears any rows with these exact quotes first, then inserts.
 *
 * Run: php artisan db:seed --class=Database\\Seeders\\TestimonialsTestSeeder
 */
class TestimonialsTestSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['author' => 'Aarti Sharma',   'role' => 'Parent of Grade III',        'quote' => 'The teachers genuinely care about every child. My daughter looks forward to school every single day.',                 'rating' => 5],
            ['author' => 'Rohan Mehta',     'role' => 'Alumnus, Class of 2018',      'quote' => 'The values I learned at Prayaag shaped my career and the person I am today. Forever grateful.',                         'rating' => 5],
            ['author' => 'Pratap Singh',    'role' => 'Father of Devika, XI',        'quote' => 'A wonderful balance of academics and activities. The faculty pushes students to be their best.',                       'rating' => 4],
            ['author' => 'Neha Gupta',      'role' => 'Parent of Grade VI',          'quote' => 'Smart classrooms and dedicated teachers make a real difference. We are very happy with the progress.',                 'rating' => 5],
            ['author' => 'Sunita Verma',    'role' => 'Parent of Grade I',           'quote' => 'The early-years programme is fantastic. My son has grown so confident in just one year.',                            'rating' => 5],
            ['author' => 'Imran Khan',      'role' => 'Parent of Grade IX',          'quote' => 'Safe, caring and well-organised. Communication with parents is excellent throughout the year.',                       'rating' => 4],
            ['author' => 'Priya Nair',      'role' => 'Alumna, Class of 2020',       'quote' => 'The science labs and library gave me a head start in college. Truly one of the top schools in Panipat.',              'rating' => 5],
            ['author' => 'Rajesh Kumar',    'role' => 'Parent of Grade VIII',        'quote' => 'Sports, music, academics — there is something for every child here. Highly recommended.',                            'rating' => 5],
            ['author' => 'Shelly Bansal',   'role' => 'Mother of Aarav, IV',         'quote' => 'The teachers track each child individually. The personal attention is what sets Prayaag apart.',                      'rating' => 5],
            ['author' => 'Deepak Malhotra', 'role' => 'Parent of Grade XII',         'quote' => 'Excellent board results and genuine career guidance. My daughter felt fully prepared and supported.',                 'rating' => 5],
        ];

        // Remove any prior copies of these (so re-running stays clean).
        Testimonial::whereIn('quote', array_column($items, 'quote'))->forceDelete();

        $order = (int) (Testimonial::query()->max('sort_order') ?? 0);

        foreach ($items as $item) {
            Testimonial::create([
                'author'       => $item['author'],
                'role'         => $item['role'],
                'quote'        => $item['quote'],
                'sort_order'   => ++$order,
                'is_published' => true,
            ]);
        }
    }
}
