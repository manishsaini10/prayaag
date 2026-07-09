<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Models\Popup\Popup;
use App\Models\Post;
use App\Models\Page;
use App\Models\PageWidget;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Slider;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user to pass authentication
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_api_list_returns_media_with_pagination_and_upload_limits(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        // Seed some media items
        Media::create([
            'disk' => 'public',
            'path' => 'uploads/image1.jpg',
            'filename' => 'image1.jpg',
            'original_name' => 'image1.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 50000, // 50 KB (small)
        ]);

        Media::create([
            'disk' => 'public',
            'path' => 'uploads/image2.jpg',
            'filename' => 'image2.jpg',
            'original_name' => 'image2.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 500000, // 500 KB (medium)
        ]);

        // Request list
        $response = $this->getJson('/admin/media-library/api');
        $response->assertOk()
            ->assertJsonCount(2, 'items')
            ->assertJsonStructure(['items', 'current_page', 'last_page', 'max_upload_size']);
    }

    public function test_api_list_filters_by_search_type_size_and_sort(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        Media::create([
            'disk' => 'public',
            'path' => 'uploads/logo.png',
            'filename' => 'logo.png',
            'original_name' => 'School Logo.png',
            'mime_type' => 'image/png',
            'size' => 80000, // small
        ]);

        Media::create([
            'disk' => 'public',
            'path' => 'uploads/doc.pdf',
            'filename' => 'doc.pdf',
            'original_name' => 'Prospectus.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1500000, // large (>1MB)
        ]);

        // Search test
        $response = $this->getJson('/admin/media-library/api?q=Logo');
        $response->assertOk()->assertJsonCount(1, 'items');
        $this->assertSame('School Logo.png', $response->json('items.0.original_name'));

        // Type test
        $response = $this->getJson('/admin/media-library/api?type=document');
        $response->assertOk()->assertJsonCount(1, 'items');
        $this->assertSame('Prospectus.pdf', $response->json('items.0.original_name'));

        // Size test
        $response = $this->getJson('/admin/media-library/api?size=large');
        $response->assertOk()->assertJsonCount(1, 'items');
        $this->assertSame('Prospectus.pdf', $response->json('items.0.original_name'));
    }

    public function test_api_upload_optimizes_images_and_stores_multiple(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $file1 = UploadedFile::fake()->image('banner.jpg', 1600, 1200);
        $file2 = UploadedFile::fake()->image('icon.png', 200, 200);

        $response = $this->postJson('/admin/media-library/api/upload', [
            'files' => [$file1, $file2],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'media');

        $media1 = Media::where('original_name', 'banner.jpg')->first();
        $this->assertNotNull($media1);
        $this->assertSame(1600, $media1->width);
        $this->assertSame(1200, $media1->height);

        // Verify optimized sizes were created
        $filename1 = pathinfo($media1->path, PATHINFO_FILENAME);
        Storage::disk('public')->assertExists($media1->path);
        Storage::disk('public')->assertExists('uploads/' . $filename1 . '-thumb.jpg');
        Storage::disk('public')->assertExists('uploads/' . $filename1 . '-medium.jpg');
        Storage::disk('public')->assertExists('uploads/' . $filename1 . '-large.jpg');
    }

    public function test_api_update_saves_alt_title_caption_description(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $media = Media::create([
            'disk' => 'public',
            'path' => 'uploads/image.jpg',
            'filename' => 'image.jpg',
            'original_name' => 'image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
        ]);

        $response = $this->putJson("/admin/media-library/api/{$media->id}", [
            'title' => 'Updated Title',
            'alt' => 'Updated Alt Text',
            'caption' => 'Updated Caption text',
            'description' => 'Updated Description text',
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $media->refresh();
        $this->assertSame('Updated Title', $media->title);
        $this->assertSame('Updated Alt Text', $media->alt);
        $this->assertSame('Updated Caption text', $media->caption);
        $this->assertSame('Updated Description text', $media->description);
    }

    public function test_api_replace_overwrites_file_and_regenerates_sizes(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        // Upload original
        $file = UploadedFile::fake()->image('original.jpg', 800, 600);
        $response = $this->postJson('/admin/media-library/api/upload', [
            'files' => [$file],
        ]);
        $media = Media::first();
        $originalFilename = pathinfo($media->path, PATHINFO_FILENAME);

        // Replace with new file of different size/dimensions
        $newFile = UploadedFile::fake()->image('new.jpg', 1920, 1080);
        $response = $this->postJson("/admin/media-library/api/{$media->id}/replace", [
            'file' => $newFile,
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $media->refresh();
        $this->assertSame(1920, $media->width);
        $this->assertSame(1080, $media->height);

        // Verify new files exist
        Storage::disk('public')->assertExists($media->path);
        Storage::disk('public')->assertExists('uploads/' . $originalFilename . '-thumb.jpg');
        Storage::disk('public')->assertExists('uploads/' . $originalFilename . '-medium.jpg');
        Storage::disk('public')->assertExists('uploads/' . $originalFilename . '-large.jpg');
    }

    public function test_media_usage_check_detects_references(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $media = Media::create([
            'disk' => 'public',
            'path' => 'uploads/target.jpg',
            'filename' => 'target.jpg',
            'original_name' => 'target.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
        ]);

        // 1. Popup usage
        $popup = Popup::create([
            'title' => 'Test Popup',
            'slug' => 'test-popup',
            'type' => 'modal',
            'status' => 'active',
            'structure' => ['blocks' => [['type' => 'image', 'src' => '/storage/uploads/target.jpg']]],
        ]);

        // Check usage API
        $response = $this->getJson("/admin/media-library/api/{$media->id}/usage");
        $response->assertOk()
            ->assertJsonPath('in_use', true)
            ->assertJsonStructure(['usage' => ['popups']]);

        $this->assertSame('Test Popup', $response->json('usage.popups.0.title'));
    }

    public function test_deleting_media_in_use_fails_unless_forced(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $media = Media::create([
            'disk' => 'public',
            'path' => 'uploads/used.jpg',
            'filename' => 'used.jpg',
            'original_name' => 'used.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
        ]);

        // Reference it in a Post
        Post::create([
            'title' => 'Post referencing image',
            'slug' => 'post-referencing-image',
            'status' => 'draft',
            'featured_image' => '/storage/uploads/used.jpg',
        ]);

        // Try standard delete - should fail
        $response = $this->deleteJson("/admin/media-library/api/{$media->id}");
        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'This media is currently in use and cannot be deleted.');

        // Delete with force=1 - should succeed
        $response = $this->deleteJson("/admin/media-library/api/{$media->id}?force=1");
        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNull(Media::find($media->id));
    }

    public function test_popup_image_link_rendering_and_click_tracking(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $popup = Popup::create([
            'title' => 'Linked Image Popup',
            'slug' => 'linked-image-popup',
            'type' => 'modal',
            'status' => 'active',
            'structure' => [
                'blocks' => [
                    [
                        'type' => 'image',
                        'src' => '/storage/uploads/target.jpg',
                        'link_url' => 'https://prayaaginternationalschool.com/admissions',
                    ]
                ]
            ],
        ]);
        $popup->refresh();

        $renderResponse = $this->getJson("/api/v1/popup/render/{$popup->id}");
        $renderResponse->assertOk();
        $this->assertStringContainsString('href="https://prayaaginternationalschool.com/admissions"', $renderResponse->json('html'));
        $this->assertStringContainsString('target="_blank"', $renderResponse->json('html'));

        $this->assertSame(0, (int)$popup->click_count);

        $trackResponse = $this->postJson('/api/v1/popup/track', [
            'popup_id' => $popup->id,
            'event_type' => 'click',
        ]);
        
        $trackResponse->assertOk()->assertJsonPath('success', true);
        
        $popup->refresh();
        $this->assertSame(1, $popup->click_count);
    }
}
