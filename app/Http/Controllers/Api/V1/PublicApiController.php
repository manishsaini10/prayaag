<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Mess\Services\MessMenuService;
use App\Http\Controllers\Controller;
use App\Models\AcademicCalendarEntry;
use App\Models\JobListing;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\VideoTestimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Api\V1\PublicApiController — Headless & Mobile V1 REST API.
 *
 * Provides versioned JSON endpoints with standardized response envelopes.
 */
class PublicApiController extends Controller
{
    public function page(string $slug): JsonResponse
    {
        $page = Page::published()->where('slug', $slug)->first();

        if (! $page) {
            return response()->json([
                'success' => false,
                'error'   => [
                    'code'    => 'PAGE_NOT_FOUND',
                    'message' => 'The requested CMS page does not exist or is not published.',
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'         => $page->id,
                'title'      => $page->title,
                'slug'       => $page->slug,
                'seo'        => $page->seo ?? [],
                'created_at' => $page->created_at?->toIso8601String(),
                'updated_at' => $page->updated_at?->toIso8601String(),
            ],
        ]);
    }

    public function messMenu(MessMenuService $service): JsonResponse
    {
        $data = $service->getActiveMenuGrouped();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function academicCalendar(): JsonResponse
    {
        $entries = AcademicCalendarEntry::orderBy('date')->get()->map(fn ($e) => [
            'id'          => $e->id,
            'title'       => $e->title,
            'date'        => $e->date,
            'category'    => $e->category,
            'description' => $e->description,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $entries,
        ]);
    }

    public function testimonials(): JsonResponse
    {
        $testimonials = Testimonial::published()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $testimonials->items(),
            'meta'    => [
                'current_page' => $testimonials->currentPage(),
                'last_page'    => $testimonials->lastPage(),
                'total'        => $testimonials->total(),
            ],
        ]);
    }

    public function videoTestimonials(): JsonResponse
    {
        $videos = VideoTestimonial::approved()
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(fn ($v) => [
                'id'                 => $v->id,
                'title'              => $v->title,
                'student_name'       => $v->student_name,
                'class_grade'        => $v->class_grade,
                'video_provider'     => $v->video_provider,
                'video_external_id'  => $v->video_external_id,
                'video_embed_url'    => $v->video_embed_url,
                'thumbnail_url'      => $v->thumbnail_url,
                'duration_seconds'   => $v->duration_seconds,
                'is_featured'        => (bool) $v->is_featured,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $videos,
        ]);
    }

    public function jobListings(): JsonResponse
    {
        $jobs = JobListing::open()->latest()->get()->map(fn ($j) => [
            'id'          => $j->id,
            'title'       => $j->title,
            'slug'        => $j->slug,
            'department'  => $j->department,
            'location'    => $j->location,
            'type'        => $j->type,
            'closes_at'   => $j->closes_at?->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $jobs,
        ]);
    }
}
