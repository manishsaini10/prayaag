<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoTestimonial;
use App\Models\VideoTestimonialView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoTestimonialAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $sortBy  = $request->input('sort', 'views_count');
        $sortDir = $request->input('dir', 'desc');

        $allowedSorts = ['views_count', 'avg_watch', 'cta_clicks', 'created_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'views_count';
        }

        // Fetch approved videos with analytics
        $videos = VideoTestimonial::where('status', 'approved')
            ->where('consent_confirmed', true)
            ->withCount(['views as total_views', 'views as cta_clicks' => function ($q) {
                $q->where('cta_clicked', true);
            }])
            ->get()
            ->map(function ($video) {
                $video->avg_watch = VideoTestimonialView::where('video_testimonial_id', $video->id)
                    ->avg('watch_percentage') ?? 0;
                return $video;
            })
            ->sortBy(function ($v) use ($sortBy) {
                return match ($sortBy) {
                    'avg_watch'  => $v->avg_watch,
                    'cta_clicks' => $v->cta_clicks,
                    'created_at' => $v->created_at,
                    default      => $v->total_views,
                };
            }, SORT_REGULAR, $sortDir === 'asc' ? false : true)
            ->values();

        $totals = [
            'total_videos' => VideoTestimonial::where('status', 'approved')->count(),
            'total_views'  => VideoTestimonialView::count(),
            'avg_watch'    => round(VideoTestimonialView::avg('watch_percentage') ?? 0, 1),
            'cta_clicks'   => VideoTestimonialView::where('cta_clicked', true)->count(),
        ];

        return view('admin.video-testimonials.analytics', compact('videos', 'totals', 'sortBy', 'sortDir'));
    }

    /** Public endpoint: record a view event (called from JS widget) */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'video_testimonial_id' => 'required|string|size:26',
            'watch_percentage'     => 'nullable|integer|min:0|max:100',
            'cta_clicked'          => 'nullable|boolean',
        ]);

        $video = VideoTestimonial::where('id', $validated['video_testimonial_id'])
            ->where('status', 'approved')
            ->where('consent_confirmed', true)
            ->first();

        if (! $video) {
            return response()->json(['ok' => false], 404);
        }

        // Privacy: hash session + IP — never store raw identifiers
        $sessionId = hash('sha256', session()->getId() . request()->ip());

        $device = 'desktop';
        $ua = request()->userAgent() ?? '';
        if (stripos($ua, 'mobile') !== false) {
            $device = 'mobile';
        } elseif (stripos($ua, 'tablet') !== false || stripos($ua, 'ipad') !== false) {
            $device = 'tablet';
        }

        VideoTestimonialView::create([
            'video_testimonial_id' => $video->id,
            'session_id'           => $sessionId,
            'watch_percentage'     => $validated['watch_percentage'] ?? 0,
            'device_type'          => $device,
            'cta_clicked'          => (bool) ($validated['cta_clicked'] ?? false),
            'viewed_at'            => now(),
        ]);

        // Bump denormalized counter
        $video->increment('views_count');

        return response()->json(['ok' => true]);
    }
}
