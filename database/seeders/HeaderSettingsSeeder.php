<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

/**
 * Seeds the "Header" settings group + the Header Settings keys read by
 * ThemeRenderer / the header partial. Idempotent (firstOrCreate by key).
 *
 *   header_variant      hv-01 | hv-02 | hv-03 | hv-04 | hv-05
 *   header_sticky       sticky header on scroll
 *   header_topbar       show the thin top information bar
 *   header_social       show social icons
 *   header_search       show the search button
 *   header_cta          show the Enquire/Apply CTA button
 *   header_login        show Student/Admin login + Pay Online
 *   header_glass        glass (frosted) header background
 *   header_transparent  transparent header that overlays the hero
 */
class HeaderSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $group = SettingGroup::updateOrCreate(
            ['slug' => 'header'],
            ['name' => 'Header', 'description' => 'Header layout, top bar & toggles', 'sort_order' => 5]
        );

        $defs = [
            ['key' => 'header_variant', 'value' => 'hv-01', 'type' => 'text'],
            ['key' => 'header_sticky', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'header_topbar', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'header_social', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'header_search', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'header_cta', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'header_login', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'header_glass', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'header_transparent', 'value' => '0', 'type' => 'boolean'],
        ];

        foreach ($defs as $d) {
            Setting::firstOrCreate(
                ['key' => $d['key']],
                ['value' => $d['value'], 'type' => $d['type'], 'group_id' => $group->id]
            );
        }
    }
}
