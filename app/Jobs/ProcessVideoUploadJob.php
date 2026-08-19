<?php

namespace App\Jobs;

use App\Core\Video\VideoManager;
use App\Models\VideoTestimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * ProcessVideoUploadJob — runs the actual provider upload in the background.
 *
 * This prevents HTTP request timeouts on slow connections / large video files.
 * Dispatched by VideoTestimonialSubmissionController when a raw file is submitted.
 */
class ProcessVideoUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Number of times the job may be attempted. */
    public int $tries = 3;

    /** Maximum seconds the job can run before being killed. */
    public int $timeout = 600; // 10 minutes for large video uploads

    public function __construct(
        public readonly string $testimonialId,
        public readonly string $tempPath,
        public readonly string $providerKey,
    ) {}

    public function handle(VideoManager $videoManager): void
    {
        $video = VideoTestimonial::find($this->testimonialId);

        if (! $video) {
            Log::warning('ProcessVideoUploadJob: VideoTestimonial not found', ['id' => $this->testimonialId]);
            return;
        }

        if (! Storage::disk('local')->exists($this->tempPath)) {
            Log::error('ProcessVideoUploadJob: temp file not found', ['path' => $this->tempPath]);
            $video->update(['status' => 'rejected', 'rejection_reason' => 'Upload file was lost before processing.']);
            return;
        }

        try {
            $fullPath = Storage::disk('local')->path($this->tempPath);

            // Create a synthetic UploadedFile to pass to provider
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($this->tempPath),
                Storage::disk('local')->mimeType($this->tempPath),
                null,
                true // testMode — marks as valid without HTTP context
            );

            $provider = $videoManager->driver($this->providerKey);
            $result   = $provider->upload($uploadedFile, [
                'title'        => $video->title,
                'student_name' => $video->student_name ?? '',
            ]);

            $video->update([
                'video_provider'    => $provider->key(),
                'video_external_id' => $result->id,
                'video_embed_url'   => $result->embedUrl,
                'thumbnail_url'     => $result->thumbnailUrl,
                'duration_seconds'  => $result->durationSeconds,
                // Keep status = 'pending' — admin still needs to review
            ]);

            Log::info('ProcessVideoUploadJob: upload complete', [
                'testimonial_id' => $this->testimonialId,
                'provider'       => $provider->key(),
                'external_id'    => $result->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('ProcessVideoUploadJob: upload failed', [
                'testimonial_id' => $this->testimonialId,
                'error'          => $e->getMessage(),
            ]);
            throw $e; // Let Laravel retry (respects $tries)

        } finally {
            // Always clean up temp file regardless of success/failure
            Storage::disk('local')->delete($this->tempPath);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessVideoUploadJob: all retries exhausted', [
            'testimonial_id' => $this->testimonialId,
            'error'          => $exception->getMessage(),
        ]);

        $video = VideoTestimonial::find($this->testimonialId);
        if ($video) {
            $video->update([
                'status'           => 'rejected',
                'rejection_reason' => 'Video upload failed after multiple attempts: ' . $exception->getMessage(),
            ]);
        }
    }
}
