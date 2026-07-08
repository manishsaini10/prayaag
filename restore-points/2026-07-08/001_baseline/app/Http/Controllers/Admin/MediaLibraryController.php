<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query()->orderBy('created_at', 'desc');

        if ($q = $request->input('q')) {
            $query->search($q);
        }

        $items = $query->paginate(36);
        $total = Media::count();

        return view('admin.media.index', [
            'items' => $items,
            'total' => $total,
            'q'     => $request->input('q', ''),
        ]);
    }

    public function loadMore(Request $request)
    {
        $query = Media::query()->orderBy('created_at', 'desc');

        if ($q = $request->input('q')) {
            $query->search($q);
        }

        $page = max((int) $request->input('page', 2), 2);
        $items = $query->paginate(36, ['*'], 'page', $page);

        return response()->json([
            'html' => [
                'grid'  => view('admin.media._items', ['items' => $items, 'mode' => 'grid'])->render(),
                'list'  => view('admin.media._items', ['items' => $items, 'mode' => 'list'])->render(),
                'large' => view('admin.media._items', ['items' => $items, 'mode' => 'large'])->render(),
            ],
            'hasMore' => $items->hasMorePages(),
            'nextPage'=> $items->currentPage() + 1,
        ]);
    }
}
