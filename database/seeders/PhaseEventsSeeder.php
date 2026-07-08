<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class PhaseEventsSeeder extends Seeder
{
    public function run(): void
    {
        Event::firstOrCreate(
            ['slug' => 'scholars-day-2024'],
            ['title' => 'Scholar\'s Day', 'starts_at' => '2024-07-15 09:00:00', 'ends_at' => '2024-07-15 13:00:00', 'location' => 'School Auditorium', 'description' => 'Celebration of academic excellence and student achievements.']
        );
        Event::firstOrCreate(
            ['slug' => 'investiture-ceremony-2024'],
            ['title' => 'Investiture Ceremony', 'starts_at' => '2024-07-01 09:00:00', 'ends_at' => '2024-07-01 13:00:00', 'location' => 'School Ground', 'description' => 'Formal induction of the student council for the academic session.']
        );
        Event::firstOrCreate(
            ['slug' => 'mothers-day-2024'],
            ['title' => 'Mother\'s Day', 'starts_at' => '2024-05-10 08:00:00', 'ends_at' => '2024-05-10 14:00:00', 'location' => 'School Premises', 'description' => 'Celebrating mothers with special performances and activities.']
        );

        $this->command?->info('Past events seeded.');
    }
}
