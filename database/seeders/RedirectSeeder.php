<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

/**
 * Known URL changes from the WordPress site → this CMS, so old indexed URLs
 * keep their SEO value instead of 404ing. Extend this list as the 404 monitor
 * surfaces more old URLs (Admin → SEO → 404 monitor).
 *
 *   php artisan db:seed --class=Database\\Seeders\\RedirectSeeder
 */
class RedirectSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            // The WordPress home page lived at /best-school-in-panipat; the CMS
            // home is now at the root.
            '/best-school-in-panipat' => '/',
        ];

        foreach ($map as $from => $to) {
            Redirect::updateOrCreate(
                ['from_path' => $from],
                ['to_path' => $to, 'status_code' => 301, 'is_active' => true]
            );
        }
    }
}
