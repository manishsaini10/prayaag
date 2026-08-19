<?php

namespace App\Http\Controllers\Admin;

use App\Core\Video\VideoManager;
use App\Events\VideoTestimonialApproved;
use App\Events\VideoTestimonialRejected;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\VideoTestimonial;
use App\Models\VideoTestimonialTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VideoTestimonialController extends Controller
{
    public function __construct(private VideoManager $videoManager) {}

    // ----------------------------------------------------------------
    // Index — moderation queue
    // ----------------------------------------------------------------

    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');
        $search = $request->input('q', '');

        $query = VideoTestimonial::withTrashed(false)
            ->with('tags')
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('submitted_by_name', 'like', "%{$search}%")
                  ->orWhere('submitted_by_email', 'like', "%{$search}%");
            });
        }

        $videos = $query->paginate(20)->appends($request->query());

        $stats = [
            'total'    => VideoTestimonial::count(),
            'pending'  => VideoTestimonial::where('status', 'pending')->count(),
            'approved' => VideoTestimonial::where('status', 'approved')->count(),
            'rejected' => VideoTestimonial::where('status', 'rejected')->count(),
            'archived' => VideoTestimonial::where('status', 'archived')->count(),
            'featured' => VideoTestimonial::where('is_featured', true)->count(),
        ];

        return view('admin.video-testimonials.index', compact('videos', 'stats', 'status', 'search'));
    }

    // ----------------------------------------------------------------
    // Create / Store
    // ----------------------------------------------------------------

    public function create(): View
    {
        return view('admin.video-testimonials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'student_name'        => 'nullable|string|max:150',
            'class_grade'         => 'nullable|string|max:50',
            'submitted_by_name'   => 'nullable|string|max:150',
            'submitted_by_email'  => 'nullable|email|max:150',
            'submitted_by_phone'  => 'nullable|string|max:30',
            'video_source'        => [
                'required', 'string', 'max:500',
                function ($attribute, $value, $fail) use ($request) {
                    $providerKey = $request->input('video_provider') ?? config('video.default_provider');
                    if ($providerKey === 'youtube_unlisted') {
                        $ytProvider = app(\App\Core\Video\Providers\YouTubeUnlistedProvider::class);
                        if (! $ytProvider->extractVideoId($value)) {
                            $fail('The provided video link or ID is not a valid YouTube URL or 11-character video ID.');
                        }
                    } elseif ($providerKey === 'instagram_reel') {
                        $igProvider = app(\App\Core\Video\Providers\InstagramReelProvider::class);
                        if (! $igProvider->extractShortcode($value)) {
                            $fail('The provided link or ID is not a valid Instagram Reel URL or shortcode.');
                        }
                    }
                },
            ],
            'video_provider'      => 'nullable|string|in:youtube_unlisted,instagram_reel,cloudflare_stream,local',
            'orientation'         => 'in:portrait,landscape',
            'consent_confirmed'   => 'boolean',
            'consent_signed_by'   => 'nullable|string|max:150',
            'cta_label'           => 'nullable|string|max:80',
            'cta_url'             => 'nullable|url|max:500',
            'sort_order'          => 'nullable|integer|min:0',
            'is_featured'         => 'boolean',
            'tags'                => 'nullable|array',
            'tags.*.tag_type'     => 'required_with:tags|in:program,event,class,department,custom',
            'tags.*.tag_value'    => 'required_with:tags|string|max:100',
        ]);

        try {
            $providerKey = $validated['video_provider'] ?? config('video.default_provider');
            $provider    = $this->videoManager->driver($providerKey);
            $result      = $provider->upload($validated['video_source'], [
                'title'       => $validated['title'],
                'student_name'=> $validated['student_name'] ?? '',
            ]);

            $video = VideoTestimonial::create([
                'title'               => $validated['title'],
                'student_name'        => $validated['student_name'] ?? null,
                'class_grade'         => $validated['class_grade'] ?? null,
                'submitted_by_name'   => $validated['submitted_by_name'] ?? null,
                'submitted_by_email'  => $validated['submitted_by_email'] ?? null,
                'submitted_by_phone'  => $validated['submitted_by_phone'] ?? null,
                'video_provider'      => $provider->key(),
                'video_external_id'   => $result->id,
                'video_embed_url'     => $result->embedUrl,
                'thumbnail_url'       => $result->thumbnailUrl,
                'duration_seconds'    => $result->durationSeconds,
                'orientation'         => $validated['orientation'] ?? 'landscape',
                'status'              => 'pending',
                'consent_confirmed'   => (bool) ($validated['consent_confirmed'] ?? false),
                'consent_signed_by'   => $validated['consent_signed_by'] ?? null,
                'consent_signed_at'   => (bool) ($validated['consent_confirmed'] ?? false) ? now() : null,
                'cta_label'           => $validated['cta_label'] ?? null,
                'cta_url'             => $validated['cta_url'] ?? null,
                'sort_order'          => $validated['sort_order'] ?? 0,
                'is_featured'         => (bool) ($validated['is_featured'] ?? false),
                'reviewed_by'         => auth()->id(),
            ]);

            if (! empty($validated['tags'])) {
                $video->syncTags($validated['tags']);
            }

            AdminNotification::record('video_testimonial', "New video testimonial added: '{$video->title}'", [
                'url'  => url('/admin/video-testimonials'),
                'icon' => 'video-camera',
            ]);

            return redirect()->route('admin.video-testimonials.index')
                ->with('success', 'Video testimonial created successfully.');

        } catch (\Throwable $e) {
            Log::error('VideoTestimonialController::store failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to process video: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Edit / Update
    // ----------------------------------------------------------------

    public function edit(string $id): View
    {
        $video = VideoTestimonial::with('tags')->findOrFail($id);
        return view('admin.video-testimonials.edit', compact('video'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $video = VideoTestimonial::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'student_name'      => 'nullable|string|max:150',
            'class_grade'       => 'nullable|string|max:50',
            'submitted_by_name' => 'nullable|string|max:150',
            'submitted_by_email'=> 'nullable|email|max:150',
            'submitted_by_phone'=> 'nullable|string|max:30',
            'video_source'      => [
                'nullable', 'string', 'max:500',
                function ($attribute, $value, $fail) use ($video) {
                    if (empty($value)) return;
                    $providerKey = $video->video_provider ?? config('video.default_provider');
                    if ($providerKey === 'youtube_unlisted') {
                        $ytProvider = app(\App\Core\Video\Providers\YouTubeUnlistedProvider::class);
                        if (! $ytProvider->extractVideoId($value)) {
                            $fail('The provided video link or ID is not a valid YouTube URL or 11-character video ID.');
                        }
                    } elseif ($providerKey === 'instagram_reel') {
                        $igProvider = app(\App\Core\Video\Providers\InstagramReelProvider::class);
                        if (! $igProvider->extractShortcode($value)) {
                            $fail('The provided link or ID is not a valid Instagram Reel URL or shortcode.');
                        }
                    }
                },
            ],
            'orientation'       => 'in:portrait,landscape',
            'consent_confirmed' => 'boolean',
            'consent_signed_by' => 'nullable|string|max:150',
            'cta_label'         => 'nullable|string|max:80',
            'cta_url'           => 'nullable|url|max:500',
            'sort_order'        => 'nullable|integer|min:0',
            'is_featured'       => 'boolean',
            'tags'              => 'nullable|array',
            'tags.*.tag_type'   => 'required_with:tags|in:program,event,class,department,custom',
            'tags.*.tag_value'  => 'required_with:tags|string|max:100',
        ]);

        $wasConsented = $video->consent_confirmed;
        $nowConsented = (bool) ($validated['consent_confirmed'] ?? false);

        $updateData = [
            'title'               => $validated['title'],
            'student_name'        => $validated['student_name'] ?? null,
            'class_grade'         => $validated['class_grade'] ?? null,
            'submitted_by_name'   => $validated['submitted_by_name'] ?? null,
            'submitted_by_email'  => $validated['submitted_by_email'] ?? null,
            'submitted_by_phone'  => $validated['submitted_by_phone'] ?? null,
            'orientation'         => $validated['orientation'] ?? $video->orientation,
            'consent_confirmed'   => $nowConsented,
            'consent_signed_by'   => $validated['consent_signed_by'] ?? $video->consent_signed_by,
            'consent_signed_at'   => ($nowConsented && ! $wasConsented) ? now() : $video->consent_signed_at,
            'cta_label'           => $validated['cta_label'] ?? null,
            'cta_url'             => $validated['cta_url'] ?? null,
            'sort_order'          => $validated['sort_order'] ?? $video->sort_order,
            'is_featured'         => (bool) ($validated['is_featured'] ?? false),
        ];

        if (! empty($validated['video_source'])) {
            $provider = $this->videoManager->driver($video->video_provider);
            $result   = $provider->upload($validated['video_source'], [
                'title' => $validated['title'],
            ]);
            $updateData['video_external_id'] = $result->id;
            $updateData['video_embed_url']   = $result->embedUrl;
            $updateData['thumbnail_url']     = $result->thumbnailUrl;
        }

        $video->update($updateData);

        if (isset($validated['tags'])) {
            $video->syncTags($validated['tags']);
        }

        return redirect()->route('admin.video-testimonials.index')
            ->with('success', 'Video testimonial updated successfully.');
    }

    // ----------------------------------------------------------------
    // Moderation: Approve
    // ----------------------------------------------------------------

    public function approve(Request $request, string $id): RedirectResponse
    {
        $video = VideoTestimonial::findOrFail($id);

        if (! $video->consent_confirmed) {
            return back()->with('error', 'Cannot approve: Consent has not been confirmed for this video.');
        }

        $video->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        VideoTestimonialApproved::dispatch($video);

        $this->bustPageCache();

        AdminNotification::record('video_testimonial', "Video testimonial approved: '{$video->title}'", [
            'level' => 'success',
            'url'   => url('/admin/video-testimonials'),
            'icon'  => 'check-circle',
        ]);

        return back()->with('success', "Video testimonial approved and is now publicly visible.");
    }

    // ----------------------------------------------------------------
    // Moderation: Reject
    // ----------------------------------------------------------------

    public function reject(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $video = VideoTestimonial::findOrFail($id);

        $video->update([
            'status'           => 'rejected',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        VideoTestimonialRejected::dispatch($video, $request->input('rejection_reason'));

        $this->bustPageCache();

        return back()->with('success', 'Video testimonial rejected.');
    }

    // ----------------------------------------------------------------
    // Delete (soft delete + provider cleanup)
    // ----------------------------------------------------------------

    public function destroy(string $id): RedirectResponse
    {
        $video = VideoTestimonial::findOrFail($id);

        try {
            $this->videoManager->driver($video->video_provider)->delete($video->video_external_id);
        } catch (\Throwable $e) {
            Log::warning('Could not delete video from provider', [
                'id' => $id, 'provider' => $video->video_provider, 'error' => $e->getMessage(),
            ]);
        }

        $video->delete(); // soft delete

        if ($video->status === 'approved') {
            $this->bustPageCache();
        }

        return redirect()->route('admin.video-testimonials.index')
            ->with('success', 'Video testimonial deleted.');
    }

    // ----------------------------------------------------------------
    // Private helpers
    // ----------------------------------------------------------------

    private function bustPageCache(): void
    {
        rescue(function () {
            $renderer = app(\App\Core\Builder\PageRenderer::class);
            foreach (\App\Models\Page::all() as $page) {
                $renderer->forget($page);
            }
        }, null, false);
    }
}
