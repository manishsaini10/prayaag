<?php

namespace Database\Seeders;

use App\Core\Theme\ThemeRenderer;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * One-shot reset: force the header back to hv-01 (the "screenshot" default —
 * logo + text, blue top bar, Enquire button, menu below) and the footer to
 * fv-01, then flush all caches so it applies immediately.
 *
 *   php artisan db:seed --class=Database\Seeders\ResetThemeSeeder
 */
class ResetThemeSeeder extends Seeder
{
    public function run(): void
    {
        Setting::where('key', 'header_variant')->update(['value' => 'hv-01']);
        Setting::where('key', 'footer_variant')->update(['value' => 'fv-01']);

        // Make the header solid + clean again (defaults), in case toggles drifted.
        Setting::where('key', 'header_glass')->update(['value' => '0']);
        Setting::where('key', 'header_transparent')->update(['value' => '0']);
        Setting::where('key', 'header_sticky')->update(['value' => '1']);
        Setting::where('key', 'header_topbar')->update(['value' => '1']);
        Setting::where('key', 'header_social')->update(['value' => '1']);
        Setting::where('key', 'header_cta')->update(['value' => '1']);
        Setting::where('key', 'header_login')->update(['value' => '1']);

        ThemeRenderer::flush();
        rescue(fn () => Cache::flush(), null, false);
    }
}
