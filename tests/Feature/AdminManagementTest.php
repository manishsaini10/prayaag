<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\PageView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['name' => 'Admin', 'email' => 'admin@a.test', 'password' => 'password']);
    }

    public function test_inboxes_require_authentication(): void
    {
        $this->get('/admin/enquiries')->assertRedirect('/login');
    }

    public function test_enquiries_inbox_lists_submissions(): void
    {
        Enquiry::create(['name' => 'Mine', 'email' => 'mine@a.test', 'message' => 'hi']);

        $this->actingAs($this->user)
            ->get('/admin/enquiries')
            ->assertOk()
            ->assertSee('mine@a.test');
    }

    public function test_applications_inbox_lists_with_position(): void
    {
        $job = JobListing::create(['title' => 'Math Teacher', 'slug' => 'math', 'status' => 'open']);
        JobApplication::create(['job_listing_id' => $job->id, 'name' => 'Jane', 'email' => 'jane@a.test']);

        $this->actingAs($this->user)
            ->get('/admin/applications')
            ->assertOk()
            ->assertSee('jane@a.test')
            ->assertSee('Math Teacher');
    }

    public function test_analytics_summarizes_views(): void
    {
        foreach (['/about', '/about', '/about', '/'] as $path) {
            PageView::create(['path' => ltrim($path, '/'), 'viewed_at' => now()]);
        }

        $this->actingAs($this->user)
            ->get('/admin/analytics')
            ->assertOk()
            ->assertSee('about')
            ->assertSee('Total page views');
    }
}
