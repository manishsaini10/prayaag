<?php

namespace Tests\Feature\Cms;

use App\Models\User;
use App\Models\VideoTestimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoTestimonialsModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_video_testimonial_enters_pending_moderation(): void
    {
        $response = $this->post('/video-testimonials/submit', [
            'student_name'       => 'Rahul Kumar',
            'class_grade'        => 'Grade 10',
            'submitted_by_name'  => 'Sunita Kumar',
            'submitted_by_email' => 'sunita@example.com',
            'title'              => 'Parent Review of Prayaag School',
            'video_url'          => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'consent_confirmed'  => '1',
            'consent_signed_by'  => 'Sunita Kumar',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('video_testimonials', [
            'student_name' => 'Rahul Kumar',
            'status'       => 'pending',
        ]);
    }

    public function test_admin_can_approve_pending_video_testimonial(): void
    {
        $admin = User::factory()->create(['two_factor_enabled' => true]);
        $admin->assignRole('admin');

        $video = VideoTestimonial::create([
            'title'               => 'Test Video',
            'submitted_by_name'   => 'Parent',
            'submitted_by_email'  => 'parent@example.com',
            'video_provider'      => 'youtube_unlisted',
            'video_external_id'   => 'abc1234',
            'status'              => 'pending',
            'consent_confirmed'   => true,
            'consent_signed_by'   => 'Parent',
        ]);

        $response = $this->actingAs($admin)
                         ->withSession(['2fa_passed' => true])
                         ->post(route('admin.video-testimonials.approve', $video->id));

        $response->assertRedirect();
        $this->assertEquals('approved', $video->fresh()->status);
        $this->assertEquals($admin->id, $video->fresh()->reviewed_by);
    }

    public function test_admin_can_reject_video_testimonial_with_reason(): void
    {
        $admin = User::factory()->create(['two_factor_enabled' => true]);
        $admin->assignRole('admin');

        $video = VideoTestimonial::create([
            'title'               => 'Low Quality Video',
            'submitted_by_name'   => 'Parent',
            'submitted_by_email'  => 'parent@example.com',
            'video_provider'      => 'youtube_unlisted',
            'video_external_id'   => 'abc12345',
            'status'              => 'pending',
            'consent_confirmed'   => true,
            'consent_signed_by'   => 'Parent',
        ]);

        $response = $this->actingAs($admin)
                         ->withSession(['2fa_passed' => true])
                         ->post(route('admin.video-testimonials.reject', $video->id), [
                             'reason' => 'Audio is unclear.',
                         ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $video->fresh()->status);
        $this->assertEquals('Audio is unclear.', $video->fresh()->rejection_reason);
    }
}
