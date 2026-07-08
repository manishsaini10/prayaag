<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_redirect_performs_a_301_and_counts_hits(): void
    {
        $r = Redirect::create([
            'from_path'   => '/old-url',
            'to_path'     => '/about',
            'status_code' => 301,
            'is_active'   => true,
        ]);

        $this->get('/old-url')
            ->assertStatus(301)
            ->assertRedirect('/about');

        $this->assertSame(1, $r->fresh()->hits);
    }

    public function test_inactive_redirect_is_ignored(): void
    {
        Redirect::create([
            'from_path'   => '/disabled',
            'to_path'     => '/about',
            'status_code' => 301,
            'is_active'   => false,
        ]);

        // No page at /disabled and the redirect is off → 404.
        $this->get('/disabled')->assertNotFound();
    }
}
