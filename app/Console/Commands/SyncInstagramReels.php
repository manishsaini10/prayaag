<?php

namespace App\Console\Commands;

use App\Models\VideoTestimonial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncInstagramReels extends Command
{
    protected $signature = 'video:sync-instagram {--limit=10 : Number of latest reels to fetch}';

    protected $description = 'Sync latest Instagram Reels automatically into Video Testimonials moderation queue';

    public function handle(): int
    {
        $userId      = config('video.instagram.user_id') ?: env('INSTAGRAM_USER_ID');
        $accessToken = config('video.instagram.access_token') ?: env('INSTAGRAM_ACCESS_TOKEN');

        if (! $userId || ! $accessToken) {
            $this->warn('Instagram API credentials not configured in .env (INSTAGRAM_USER_ID / INSTAGRAM_ACCESS_TOKEN).');
            $this->info('You can still import Instagram Reels manually by pasting the Reel URL in Admin Panel.');
            return Command::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $this->info("Fetching latest {$limit} Reels from Instagram Account...");

        try {
            $response = Http::timeout(10)->get("https://graph.facebook.com/v18.0/{$userId}/media", [
                'fields'       => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                'access_token' => $accessToken,
                'limit'        => $limit,
            ]);

            if ($response->failed()) {
                $this->error('Instagram Graph API Error: ' . $response->body());
                return Command::FAILURE;
            }

            $mediaItems = $response->json()['data'] ?? [];
            $imported = 0;

            foreach ($mediaItems as $item) {
                // Only process VIDEO / REELS
                if (($item['media_type'] ?? '') !== 'VIDEO') {
                    continue;
                }

                $id           = $item['id'];
                $caption      = $item['caption'] ?? 'Instagram Reel';
                $thumbnailUrl = $item['thumbnail_url'] ?? $item['media_url'] ?? null;
                $permalink    = $item['permalink'] ?? "https://www.instagram.com/p/{$id}/";

                // Check if already exists
                $exists = VideoTestimonial::where('video_provider', 'instagram_reel')
                    ->where('video_external_id', $id)
                    ->exists();

                if (! $exists) {
                    VideoTestimonial::create([
                        'title'             => \Illuminate\Support\Str::limit($caption, 200),
                        'video_provider'    => 'instagram_reel',
                        'video_external_id' => $id,
                        'video_embed_url'   => "https://www.instagram.com/p/{$id}/embed/",
                        'thumbnail_url'     => $thumbnailUrl,
                        'status'            => 'pending', // awaits admin moderation
                        'consent_confirmed' => true,
                        'consent_signed_by' => 'Instagram Sync',
                        'consent_signed_at' => now(),
                    ]);
                    $imported++;
                    $this->info("Imported Reel: {$caption}");
                }
            }

            $this->info("Sync completed! Imported {$imported} new Instagram Reel(s).");
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('SyncInstagramReels command failed', ['error' => $e->getMessage()]);
            $this->error("Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
