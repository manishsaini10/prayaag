<?php

namespace Database\Seeders;

use App\Models\JobListing;
use Illuminate\Database\Seeder;

/**
 * Seeds a couple of open job listings.
 */
class Phase12Seeder extends Seeder
{
    public function run(): void
    {
        JobListing::firstOrCreate(
            ['slug' => 'mathematics-teacher'],
            [
                'title'           => 'Mathematics Teacher',
                'department'      => 'Academics',
                'location'        => 'On-site',
                'employment_type' => 'full_time',
                'description'     => '<p>We are seeking a passionate mathematics teacher.</p>',
                'status'          => 'open',
            ]
        );

        JobListing::firstOrCreate(
            ['slug' => 'administrative-assistant'],
            [
                'title'           => 'Administrative Assistant',
                'department'      => 'Operations',
                'location'        => 'On-site',
                'employment_type' => 'full_time',
                'description'     => '<p>Support the front office and admissions team.</p>',
                'status'          => 'open',
            ]
        );
    }
}
