<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_404_when_no_home_page_exists(): void
    {
        // Single-site: "/" renders the page with slug "home". With an empty
        // database there is none, so the front controller 404s (firstOrFail).
        $this->get('/')->assertNotFound();
    }

    public function test_home_renders_when_a_published_home_page_exists(): void
    {
        Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $this->get('/')->assertOk();
    }
}
