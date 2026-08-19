<?php

namespace App\Console\Commands;

use App\Core\Video\VideoManager;
use App\Models\VideoTestimonial;
use Illuminate\Console\Command;

class BackfillVideoThumbnails extends Command
{
    protected $signature = 'video:backfill-thumbnails';

    protected $description = 'Backfill missing thumbnail_url values for existing video testimonials';

    public function handle(VideoManager $videoManager): int
    {
        $videos = VideoTestimonial::where(function ($q) {
            $q->whereNull('thumbnail_url')
              ->orWhere('thumbnail_url', '');
        })->get();

        if ($videos->isEmpty()) {
            $this->info('No video testimonials need thumbnail backfilling.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($videos as $video) {
            try {
                $provider = $videoManager->driver($video->video_provider ?: 'youtube_unlisted');
                $thumbUrl = $provider->getThumbnailUrl($video->video_external_id);

                if ($thumbUrl) {
                    $video->update(['thumbnail_url' => $thumbUrl]);
                    $this->info("Backfilled thumbnail for video ID {$video->id} ({$video->title}) -> {$thumbUrl}");
                    $count++;
                }
            } catch (\Throwable $e) {
                $this->error("Failed to backfill video ID {$video->id}: {$e->getMessage()}");
            }
        }

        $this->info("Completed! Backfilled {$count} video thumbnail(s).");
        return Command::SUCCESS;
    }
}
