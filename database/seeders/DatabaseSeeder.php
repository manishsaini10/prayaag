<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Phase2Seeder::class,
            Phase3Seeder::class,
            Phase4Seeder::class,
            Phase5Seeder::class,
            Phase6Seeder::class,
            Phase10Seeder::class,
            Phase11Seeder::class,
            Phase12Seeder::class,
            Phase13Seeder::class,
            SiteChromeSeeder::class,
            HomePageSeeder::class,
            MediaImageSeeder::class,
        ]);
    }
}
