<?php

namespace Tests\Feature;

use App\Core\Builder\WidgetRegistry;
use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_scope_excludes_closed_and_expired(): void
    {
        JobListing::create(['title' => 'Open', 'slug' => 'open', 'status' => 'open']);
        JobListing::create(['title' => 'Closed', 'slug' => 'closed', 'status' => 'closed']);
        JobListing::create(['title' => 'Expired', 'slug' => 'expired', 'status' => 'open', 'closes_at' => now()->subDay()]);

        $this->assertSame(1, JobListing::open()->count());
    }

    public function test_public_can_apply_to_an_open_job_with_resume(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $job = JobListing::create(['title' => 'Teacher', 'slug' => 'teacher', 'status' => 'open']);

        $this->post('/jobs/apply', [
            'job_listing_id' => $job->id,
            'name'           => 'Jane',
            'email'          => 'jane@example.test',
            'cover_letter'   => 'I would love to join.',
            'resume'         => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'),
        ])->assertRedirect();

        $this->assertDatabaseHas('job_applications', [
            'email'          => 'jane@example.test',
            'job_listing_id' => $job->id,
        ]);

        $this->assertNotNull(JobApplication::first()->resume_media_id);
    }

    public function test_cannot_apply_to_a_closed_job(): void
    {
        $job = JobListing::create(['title' => 'Closed', 'slug' => 'closed', 'status' => 'closed']);

        $this->post('/jobs/apply', [
            'job_listing_id' => $job->id,
            'name'           => 'Jane',
            'email'          => 'jane@example.test',
        ])->assertNotFound();

        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        $job = JobListing::create(['title' => 'Open', 'slug' => 'open', 'status' => 'open']);

        $this->post('/jobs/apply', [
            'job_listing_id' => $job->id,
            'name'           => 'Bot',
            'email'          => 'bot@example.test',
            'website'        => 'http://spam.example',
        ])->assertRedirect();

        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_job_listings_widget_renders_open_jobs(): void
    {
        JobListing::create(['title' => 'Mathematics Teacher', 'slug' => 'math', 'status' => 'open', 'department' => 'Academics']);

        $html = app(WidgetRegistry::class)->render('job_listings', ['limit' => 5]);

        $this->assertStringContainsString('Mathematics Teacher', $html);
        $this->assertStringContainsString('Academics', $html);
    }
}
