<?php

namespace Tests\Feature;

use App\Core\Media\MediaManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_storing_an_image_creates_a_media_record(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.jpg', 640, 480);
        $media = app(MediaManager::class)->store($file);

        $this->assertSame('photo.jpg', $media->original_name);
        $this->assertSame(640, $media->width);
        $this->assertSame(480, $media->height);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_deleting_media_removes_the_record_and_file(): void
    {
        Storage::fake('public');

        $media = app(MediaManager::class)->store(UploadedFile::fake()->image('a.jpg'));
        Storage::disk('public')->assertExists($media->path);

        app(MediaManager::class)->delete($media);

        Storage::disk('public')->assertMissing($media->path);
        $this->assertNull(\App\Models\Media::find($media->id));
    }
}
