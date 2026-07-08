<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Notice;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds demo content for the school: a category, posts, notices, and events,
 * so the dynamic widgets have real data to bind to.
 */
class Phase10Seeder extends Seeder
{
    public function run(): void
    {
        $news = Category::firstOrCreate(['slug' => 'news'], ['name' => 'News']);
        $tag = Tag::firstOrCreate(['slug' => 'announcement'], ['name' => 'Announcement']);

        foreach (['Admissions Open for 2026', 'Annual Sports Day Recap', 'New Science Lab Inaugurated'] as $i => $title) {
            $post = Post::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'category_id'  => $news->id,
                    'title'        => $title,
                    'excerpt'      => 'A short summary of "' . $title . '".',
                    'body'         => '<p>Full article body for ' . $title . '.</p>',
                    'status'       => 'published',
                    'published_at' => now()->subDays($i),
                ]
            );
            $post->tags()->syncWithoutDetaching([$tag->id]);
        }

        Notice::firstOrCreate(
            ['title' => 'School reopens on April 1'],
            ['body' => 'Classes resume after the spring break.', 'is_pinned' => true, 'starts_at' => now()->subDay()]
        );
        Notice::firstOrCreate(
            ['title' => 'Fee submission deadline: April 15'],
            ['body' => 'Please submit term fees before the deadline.']
        );

        Event::firstOrCreate(
            ['slug' => 'annual-day-2026'],
            ['title' => 'Annual Day 2026', 'starts_at' => now()->addWeeks(2), 'location' => 'Main Auditorium']
        );
        Event::firstOrCreate(
            ['slug' => 'parent-teacher-meeting'],
            ['title' => 'Parent-Teacher Meeting', 'starts_at' => now()->addWeeks(4), 'location' => 'Classrooms']
        );
    }
}
