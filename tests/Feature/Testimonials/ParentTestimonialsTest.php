<?php

namespace Tests\Feature\Testimonials;

use App\Models\Page;
use App\Models\Testimonial;
use App\Models\User;
use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParentTestimonialsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear public directory mock uploads after each test run
        $this->cleanUpUploads();
    }

    protected function tearDown(): void
    {
        $this->cleanUpUploads();
        parent::tearDown();
    }

    protected function cleanUpUploads(): void
    {
        $dir = public_path('uploads/testimonials_test');
        if (file_exists($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            @rmdir($dir);
        }
    }

    public function test_parent_can_submit_valid_testimonial_json(): void
    {
        Mail::fake();

        $response = $this->postJson(route('testimonials.store'), [
            'name'         => 'Ramesh Kumar',
            'phone'        => '9876543210',
            'student_name' => 'Aditya',
            'class'        => 'Grade V',
            'relation'     => 'Father',
            'email'        => 'ramesh@example.com',
            'title'        => 'Exceptional Learning Environment',
            'testimonial'  => 'This school provides a very nurturing environment and the teachers are highly qualified and very supportive throughout the academic session.',
            'rating'       => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Ramesh Kumar',
            'phone' => '9876543210',
            'status' => 'pending', // defaults to pending
        ]);

        $this->assertDatabaseHas('admin_notifications', [
            'type'  => 'testimonial',
            'title' => 'New testimonial from Ramesh Kumar is pending moderation',
        ]);

        // Legacy compatibility sync assertions
        $t = Testimonial::where('name', 'Ramesh Kumar')->first();
        $this->assertEquals('Ramesh Kumar', $t->author);
        $this->assertEquals($t->testimonial, $t->quote);
        $this->assertFalse($t->is_published);
        $this->assertFalse($t->featured);
    }

    public function test_submission_fails_on_min_character_validation(): void
    {
        $response = $this->postJson(route('testimonials.store'), [
            'name'         => 'Sita Devi',
            'phone'        => '9876543222',
            'testimonial'  => 'Too short quote.', // Less than min 50 chars default
            'rating'       => 5,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);
    }

    public function test_spam_filter_blocks_profanity_keywords(): void
    {
        $response = $this->postJson(route('testimonials.store'), [
            'name'         => 'Sita Devi',
            'phone'        => '9876543222',
            'testimonial'  => 'This school is a fraud and they cheat people. Total abuse of our trust.', // contains "fraud", "cheat", "abuse"
            'rating'       => 5,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
    }

    public function test_duplicate_submissions_are_blocked(): void
    {
        $payload = [
            'name'         => 'Duplicate Parent',
            'phone'        => '9876543200',
            'testimonial'  => 'We had an amazing experience with the teachers who are very supportive throughout the academic session.',
            'rating'       => 5,
        ];

        // First submit - success
        $this->postJson(route('testimonials.store'), $payload)->assertStatus(200);

        // Second submit - duplicate block
        $response = $this->postJson(route('testimonials.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
    }

    public function test_photo_is_center_cropped_and_stored(): void
    {
        Mail::fake();

        $photo = UploadedFile::fake()->image('avatar.jpg', 1200, 800);

        $response = $this->postJson(route('testimonials.store'), [
            'name'         => 'Image Submitter',
            'phone'        => '9876543211',
            'testimonial'  => 'We had an amazing experience with the teachers who are very supportive throughout the academic session.',
            'rating'       => 5,
            'photo'        => $photo,
        ]);

        $response->assertStatus(200);
        
        $t = Testimonial::where('name', 'Image Submitter')->first();
        $this->assertNotNull($t->image);
        $this->assertStringContainsString('uploads/testimonials_test/main_', $t->image);

        // Verify physical files exist and are correct dimensions (GD cropped)
        $mainPath = public_path($t->image);
        $thumbPath = str_replace('main_', 'thumb_', $mainPath);

        $this->assertFileExists($mainPath);
        $this->assertFileExists($thumbPath);

        // Verify dimensions (main is 800x800, thumb is 250x250)
        list($mWidth, $mHeight) = getimagesize($mainPath);
        list($tWidth, $tHeight) = getimagesize($thumbPath);

        $this->assertEquals(800, $mWidth);
        $this->assertEquals(800, $mHeight);
        $this->assertEquals(250, $tWidth);
        $this->assertEquals(250, $tHeight);
    }

    public function test_admin_can_approve_testimonial(): void
    {
        Mail::fake();
        $admin = User::factory()->create();

        $t = Testimonial::create([
            'name'        => 'Moderation Test Parent',
            'phone'       => '9876543222',
            'testimonial' => 'This is a beautiful test quote containing enough length characters to satisfy the min length constraint.',
            'status'      => 'pending',
            'email'       => 'parent@example.com',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.testimonials-console.approve', $t->id));

        $response->assertStatus(302); // Redirect back
        $t->refresh();

        $this->assertEquals('approved', $t->status);
        $this->assertTrue($t->is_published); // legacy sync check
        $this->assertEquals($admin->id, $t->approved_by);
        $this->assertNotNull($t->approved_at);
    }

    public function test_admin_can_reject_testimonial(): void
    {
        Mail::fake();
        $admin = User::factory()->create();

        $t = Testimonial::create([
            'name'        => 'Moderation Test Parent',
            'phone'       => '9876543222',
            'testimonial' => 'This is a beautiful test quote containing enough length characters to satisfy the min length constraint.',
            'status'      => 'pending',
            'email'       => 'parent@example.com',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.testimonials-console.reject', $t->id));

        $response->assertStatus(302);
        $t->refresh();

        $this->assertEquals('rejected', $t->status);
        $this->assertFalse($t->is_published);
    }

    public function test_admin_can_toggle_verified(): void
    {
        $admin = User::factory()->create();

        $t = Testimonial::create([
            'name'        => 'Verify Me',
            'phone'       => '1112223333',
            'testimonial' => 'This testimonial should be verifiable by the admin at any point.',
            'status'      => 'approved',
        ]);

        $this->assertNull($t->is_verified);

        $this->actingAs($admin);
        $this->post(route('admin.testimonials-console.toggle-verified', $t->id));

        $t->refresh();
        $this->assertTrue($t->is_verified);

        // Toggle off
        $this->post(route('admin.testimonials-console.toggle-verified', $t->id));
        $t->refresh();
        $this->assertFalse($t->is_verified);
    }

    public function test_admin_can_export_csv(): void
    {
        $admin = User::factory()->create();
        Testimonial::create([
            'name' => 'CSV Export', 'phone' => '9998887777',
            'testimonial' => 'This testimonial should be in the exported CSV file for verification.',
            'status' => 'approved',
        ]);

        $this->actingAs($admin);
        $response = $this->get(route('admin.testimonials-console.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('CSV Export', $content);
        $this->assertStringContainsString('9998887777', $content);
    }

    public function test_admin_can_bulk_action_approve_and_delete(): void
    {
        $admin = User::factory()->create();

        $t1 = Testimonial::create([
            'name' => 'Convo 1', 'phone' => '123',
            'testimonial' => 'Test testimonial quotes content goes here and it is long.',
            'status' => 'pending'
        ]);
        $t2 = Testimonial::create([
            'name' => 'Convo 2', 'phone' => '456',
            'testimonial' => 'Another testimonial quotes content goes here and it is long.',
            'status' => 'pending'
        ]);

        $this->actingAs($admin);

        // Bulk Approve
        $response = $this->post(route('admin.testimonials-console.bulk'), [
            'action' => 'approve',
            'ids'    => [$t1->id, $t2->id],
        ]);

        $response->assertStatus(302);
        $this->assertEquals('approved', $t1->refresh()->status);
        $this->assertEquals('approved', $t2->refresh()->status);

        // Bulk Delete
        $response = $this->post(route('admin.testimonials-console.bulk'), [
            'action' => 'delete',
            'ids'    => [$t1->id, $t2->id],
        ]);

        $response->assertStatus(302);
        $this->assertSoftDeleted('testimonials', ['id' => $t1->id]);
        $this->assertSoftDeleted('testimonials', ['id' => $t2->id]);
    }

    public function test_post_testimonial_page_renders_successfully(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'post-testimonial'],
            ['title' => 'Post Your Testimonial', 'status' => 'published']
        );
        $page->update([
            'status' => 'published',
            'seo' => ['title' => 'Post Your Testimonial', 'description' => 'Share your experience'],
        ]);
        app(PageTreeService::class)->sync($page, [
            [
                'type' => 'flush',
                'rows' => [
                    [
                        'columns' => [
                            [
                                'width' => 12,
                                'widgets' => [
                                    ['type' => 'testimonial_page', 'settings' => []],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        app(PageRenderer::class)->forget($page);

        $response = $this->get('/post-testimonial');

        $response->assertStatus(200);
        $response->assertSee('Post Your Testimonial');
        $response->assertSee('Share Your Experience');
    }
}
