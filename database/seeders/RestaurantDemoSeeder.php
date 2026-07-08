<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * RETIRED. This seeder built a second vertical ("Bella Cucina") on a second
 * tenant to prove the engine generalized across tenants. With multi-tenancy
 * removed (single-site CMS), a second site no longer has meaning, and its
 * pages/menus/layout would collide with the primary site on the now-global
 * unique constraints (slug, type+slug, etc.).
 *
 * Intentionally a no-op. It is no longer referenced by DatabaseSeeder. The
 * file is retained only because the tooling cannot delete it; safe to remove.
 */
class RestaurantDemoSeeder extends Seeder
{
    public function run(): void
    {
        // no-op (see class docblock)
    }
}
