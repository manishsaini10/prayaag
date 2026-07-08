<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_is_recorded_on_create(): void
    {
        $page = Page::create(['title' => 'Logged', 'slug' => 'logged', 'status' => 'draft']);

        $this->assertDatabaseHas('activity_logs', [
            'log_name'     => 'pages',
            'subject_type' => Page::class,
            'subject_id'   => $page->id,
        ]);
    }
}
