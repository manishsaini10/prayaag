<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsManager;
use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

/**
 * Normalises the Settings tabs: ensures every settings group exists (with a
 * stable slug + sort order) and assigns each setting key to the group it
 * actually belongs to. Idempotent — safe to re-run anytime.
 *
 * Run: php artisan db:seed --class=Database\\Seeders\\SettingsRegroupSeeder
 */
class SettingsRegroupSeeder extends Seeder
{
    /** Groups in tab order. */
    protected array $groups = [
        ['slug' => 'general', 'name' => 'General',  'sort_order' => 1, 'description' => 'Site name, logo & branding'],
        ['slug' => 'seo',     'name' => 'SEO',      'sort_order' => 2, 'description' => 'Search engine metadata'],
        ['slug' => 'contact', 'name' => 'Contact',  'sort_order' => 3, 'description' => 'Phone, email, address & map'],
        ['slug' => 'social',  'name' => 'Social',   'sort_order' => 4, 'description' => 'Social media profiles'],
        ['slug' => 'header',  'name' => 'Header',   'sort_order' => 5, 'description' => 'Header layout, top bar & toggles'],
        ['slug' => 'theme',   'name' => 'Theme',    'sort_order' => 6, 'description' => 'Colours & appearance'],
    ];

    /** key => group slug */
    protected array $map = [
        // General / brand
        'site_name'           => 'general',
        'site_tagline'        => 'general',
        'site_about'          => 'general',
        'site_logo'           => 'general',

        // SEO
        'meta_description'    => 'seo',

        // Contact
        'contact_email'       => 'contact',
        'contact_phone'       => 'contact',
        'contact_address'     => 'contact',
        'google_map_embed'    => 'contact',

        // Social
        'social_facebook'     => 'social',
        'social_instagram'    => 'social',
        'social_twitter'      => 'social',
        'social_linkedin'     => 'social',
        'social_youtube'      => 'social',

        // Header — top utility bar + login/pay links + the header CTA button
        'top_note_1'          => 'header',
        'top_note_2'          => 'header',
        'student_login_url'   => 'header',
        'admin_login_url'     => 'header',
        'online_payment_url'  => 'header',
        'header_badge_image'  => 'header',
        'admission_cta_label' => 'header',
        'admission_cta_url'   => 'header',

        // Header — layout & toggles
        'header_variant'      => 'header',
        'header_sticky'       => 'header',
        'header_topbar'       => 'header',
        'header_social'       => 'header',
        'header_search'       => 'header',
        'header_cta'          => 'header',
        'header_login'        => 'header',
        'header_glass'        => 'header',
        'header_transparent'  => 'header',

        // Theme
        'theme_primary_color' => 'theme',
    ];

    public function run(): void
    {
        // 1) Ensure every group exists with a stable slug + order.
        $idBySlug = [];
        foreach ($this->groups as $g) {
            $group = SettingGroup::updateOrCreate(
                ['slug' => $g['slug']],
                ['name' => $g['name'], 'sort_order' => $g['sort_order'], 'description' => $g['description']]
            );
            $idBySlug[$g['slug']] = $group->id;
        }

        // 1b) Remove any duplicate "Header" group that was created without a slug.
        SettingGroup::whereNull('slug')->where('name', 'Header')->delete();
        SettingGroup::where('slug', '')->delete();

        // 2) Move each known setting into its correct group.
        foreach ($this->map as $key => $slug) {
            Setting::where('key', $key)->update(['group_id' => $idBySlug[$slug] ?? null]);
        }

        // 3) Refresh the cached settings map.
        app(SettingsManager::class)->flush();
    }
}
