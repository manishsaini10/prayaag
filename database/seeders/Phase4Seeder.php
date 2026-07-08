<?php

namespace Database\Seeders;

use App\Models\MediaFolder;
use Illuminate\Database\Seeder;

/**
 * Seeds a starter folder tree for the media library.
 */
class Phase4Seeder extends Seeder
{
    public function run(): void
    {
        foreach (['Images', 'Documents', 'Galleries'] as $name) {
            MediaFolder::firstOrCreate(
                ['parent_id' => null, 'name' => $name]
            );
        }
    }
}
