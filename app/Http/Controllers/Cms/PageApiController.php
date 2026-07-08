<?php

namespace App\Http\Controllers\Cms;

use App\Core\Builder\PageTreeService;
use App\Core\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin API for pages and their builder tree (single-site). Permission gating
 * is applied on the routes.
 */
class PageApiController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(Page::query()->latest()->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'required|string|max:255',
            'status'    => 'nullable|string|in:draft,published',
            'layout_id' => 'nullable|string',
            'seo'       => 'nullable|array',
        ]);

        $page = Page::create($data);

        return ApiResponse::success($page, [], 201);
    }

    public function show(string $page): JsonResponse
    {
        $model = Page::with('sections.rows.columns.widgets')->findOrFail($page);

        return ApiResponse::success($model);
    }

    public function update(Request $request, string $page): JsonResponse
    {
        $model = Page::findOrFail($page);

        $data = $request->validate([
            'title'     => 'sometimes|string|max:255',
            'slug'      => 'sometimes|string|max:255',
            'status'    => 'nullable|string|in:draft,published',
            'layout_id' => 'nullable|string',
            'seo'       => 'nullable|array',
        ]);

        $model->update($data);

        return ApiResponse::success($model->fresh());
    }

    public function destroy(string $page): JsonResponse
    {
        Page::findOrFail($page)->delete();

        return ApiResponse::success(null);
    }

    public function syncTree(Request $request, string $page, PageTreeService $tree): JsonResponse
    {
        $model = Page::findOrFail($page);

        $sections = $request->validate([
            'sections' => 'present|array',
        ])['sections'];

        $tree->sync($model, $sections);

        return ApiResponse::success(
            $model->fresh()->load('sections.rows.columns.widgets')
        );
    }
}
