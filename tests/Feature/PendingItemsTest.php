<?php

namespace Tests\Feature;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\WidgetRegistry;
use App\Models\Enquiry;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Page;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Covers the deferred items closed in the "complete all pendings" pass:
 * private résumé storage + gated download, admin write-actions, the dynamic
 * widget cache bypass, and per-page SEO meta output.
 */
class PendingItemsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['name' => 'Admin', 'email' => 'admin@a.test', 'password' => 'password']);
    }

    public function test_resume_is_stored_privately_and_download_is_gated(): void
    {
        Storage::fake('local');
        $job = JobListing::create(['title' => 'Teacher', 'slug' => 'teacher', 'status' => 'open']);

        $this->post('/jobs/apply', [
            'job_listing_id' => $job->id,
            'name'           => 'Jane',
            'email'          => 'jane@a.test',
            'resume'         => UploadedFile::fake()->create('cv.pdf', 80, 'application/pdf'),
        ])->assertRedirect();

        $application = JobApplication::first();
        $this->assertNotNull($application->resume_media_id);
        $this->assertSame('local', $application->resume->disk);

        // Gated: guests are bounced to login.
        $this->get('/admin/applications/' . $application->id . '/resume')->assertRedirect('/login');

        // Authenticated admin can download.
        $this->actingAs($this->user)
            ->get('/admin/applications/' . $application->id . '/resume')
            ->assertOk();
    }

    public function test_admin_can_update_enquiry_status(): void
    {
        $enquiry = Enquiry::create(['name' => 'X', 'email' => 'x@a.test', 'message' => 'hi']);

        $this->actingAs($this->user)
            ->post('/admin/enquiries/' . $enquiry->id . '/status', ['status' => 'read'])
            ->assertRedirect();

        $this->assertSame('read', $enquiry->fresh()->status);
    }

    public function test_admin_can_unsubscribe_a_subscriber(): void
    {
        $sub = Subscriber::create(['email' => 's@a.test', 'status' => 'subscribed', 'subscribed_at' => now()]);

        $this->actingAs($this->user)
            ->post('/admin/subscribers/' . $sub->id . '/unsubscribe')
            ->assertRedirect();

        $this->assertSame('unsubscribed', $sub->fresh()->status);
    }

    public function test_admin_can_update_application_status(): void
    {
        $job = JobListing::create(['title' => 'Teacher', 'slug' => 'teacher', 'status' => 'open']);
        $app = JobApplication::create(['job_listing_id' => $job->id, 'name' => 'Jane', 'email' => 'jane@a.test']);

        $this->actingAs($this->user)
            ->post('/admin/applications/' . $app->id . '/status', ['status' => 'hired'])
            ->assertRedirect();

        $this->assertSame('hired', $app->fresh()->status);
    }

    public function test_widgets_declare_dynamic_correctly(): void
    {
        $registry = app(WidgetRegistry::class);

        $this->assertTrue($registry->get('latest_posts')->isDynamic());
        $this->assertTrue($registry->get('contact_form')->isDynamic());
        $this->assertFalse($registry->get('heading')->isDynamic());
    }

    public function test_pages_with_dynamic_widgets_are_not_stale_cached(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);
        $section = $page->sections()->create(['section_type' => 'section', 'sort_order' => 0]);
        $row = $section->rows()->create(['sort_order' => 0]);
        $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);
        $column->widgets()->create(['widget_type' => 'latest_posts', 'sort_order' => 0, 'settings' => ['limit' => 5]]);

        $renderer = app(PageRenderer::class);

        Post::create(['title' => 'First Post', 'slug' => 'first', 'status' => 'published', 'published_at' => now()]);
        $this->assertStringContainsString('First Post', $renderer->renderCached($page));

        Post::create(['title' => 'Second Post', 'slug' => 'second', 'status' => 'published', 'published_at' => now()]);
        // If the dynamic page had been cached, "Second Post" would be missing.
        $this->assertStringContainsString('Second Post', $renderer->renderCached($page->fresh()));
    }

    public function test_page_emits_seo_meta_tags(): void
    {
        Page::create([
            'title'  => 'About',
            'slug'   => 'about',
            'status' => 'published',
            'seo'    => ['description' => 'About our school', 'keywords' => ['school', 'about']],
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('About our school', false)
            ->assertSee('og:title', false);
    }
}
