<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Contracts\View\View;

/**
 * First-party analytics summary from the page_views table (single-site).
 */
class AnalyticsController extends Controller
{
    public function index(): View
    {
        $total = PageView::count();
        $last7 = PageView::where('viewed_at', '>=', now()->subDays(7))->count();

        $topPages = PageView::selectRaw('path, COUNT(*) as views')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(25)
            ->get();

        return view('admin.analytics', [
            'total'    => $total,
            'last7'    => $last7,
            'topPages' => $topPages,
        ]);
    }
}
