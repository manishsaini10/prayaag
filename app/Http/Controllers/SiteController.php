<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\View\View;

/**
 * Public marketing site (migrated from WordPress). For now this serves the
 * rebuilt home page as a self-contained Blade view. As more pages are migrated,
 * add methods here (about, admissions, etc.) or move content into the CMS.
 */
class SiteController extends Controller
{
    public function home(): View
    {
        $testimonials = Testimonial::published()
            ->featured()
            ->forLocation('home')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('site.home', [
            'dynamicTestimonials' => $testimonials,
        ]);
    }
}
