<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class VideoTestimonialSettingsController extends Controller
{
    private string $settingsFile;

    public function __construct()
    {
        $this->settingsFile = storage_path('app/video_testimonial_settings.json');
    }

    public function index()
    {
        $settings = $this->getSettings();
        return view('admin.video-testimonials.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_provider'          => 'required|string|in:youtube_unlisted,instagram_reel,cloudflare_stream,local',
            'default_layout'            => 'required|string|in:grid,carousel,reel_slider,masonry,spotlight,wall_mosaic,story_bubble',
            'default_card_style'        => 'required|string|in:minimal,shadow,glassmorphism,fullscreen_immersive,story_style',
            'section_bg'                => 'nullable|string|max:100',
            'text_color'                => 'nullable|string|max:50',
            'border_radius'             => 'nullable|string|max:50',
            'instagram_user_id'         => 'nullable|string|max:200',
            'instagram_access_token'    => 'nullable|string|max:1000',
            'enable_public_submissions' => 'boolean',
            'require_parent_consent'    => 'boolean',
            'auto_approve'              => 'boolean',
        ]);

        $validated['enable_public_submissions'] = $request->has('enable_public_submissions');
        $validated['require_parent_consent']    = $request->has('require_parent_consent');
        $validated['auto_approve']              = $request->has('auto_approve');

        File::put($this->settingsFile, json_encode($validated, JSON_PRETTY_PRINT));

        // Also update .env for Instagram credentials if provided
        if (! empty($validated['instagram_user_id'])) {
            $this->updateEnv(['INSTAGRAM_USER_ID' => $validated['instagram_user_id']]);
        }
        if (! empty($validated['instagram_access_token'])) {
            $this->updateEnv(['INSTAGRAM_ACCESS_TOKEN' => $validated['instagram_access_token']]);
        }

        return redirect()->route('admin.video-testimonials.settings')
            ->with('success', 'Video Testimonial Settings updated successfully!');
    }

    public function syncInstagram()
    {
        try {
            Artisan::call('video:sync-instagram');
            $output = Artisan::output();
            return redirect()->route('admin.video-testimonials.settings')
                ->with('success', "Instagram Sync Completed: {$output}");
        } catch (\Throwable $e) {
            Log::error('Admin Instagram Sync Failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.video-testimonials.settings')
                ->with('error', "Instagram Sync Error: {$e->getMessage()}");
        }
    }

    private function getSettings(): array
    {
        if (File::exists($this->settingsFile)) {
            $json = json_decode(File::get($this->settingsFile), true);
            if (is_array($json)) {
                return array_merge($this->defaultSettings(), $json);
            }
        }
        return $this->defaultSettings();
    }

    private function defaultSettings(): array
    {
        return [
            'default_provider'          => config('video.default_provider', 'youtube_unlisted'),
            'default_layout'            => 'reel_slider',
            'default_card_style'        => 'shadow',
            'section_bg'                => 'transparent',
            'text_color'                => '#0f172a',
            'border_radius'             => '1rem',
            'instagram_user_id'         => env('INSTAGRAM_USER_ID', ''),
            'instagram_access_token'    => env('INSTAGRAM_ACCESS_TOKEN', ''),
            'enable_public_submissions' => true,
            'require_parent_consent'    => true,
            'auto_approve'              => false,
        ];
    }

    private function updateEnv(array $data): void
    {
        $envFile = base_path('.env');
        if (! File::exists($envFile)) return;

        $content = File::get($envFile);
        foreach ($data as $key => $value) {
            $value = trim($value);
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
            } else {
                $content .= "\n{$key}=\"{$value}\"";
            }
        }
        File::put($envFile, $content);
    }
}
