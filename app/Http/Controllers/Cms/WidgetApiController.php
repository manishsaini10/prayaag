<?php

namespace App\Http\Controllers\Cms;

use App\Core\Builder\WidgetRegistry;
use App\Core\Http\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Exposes the registered widget catalogue for the editor's widget palette.
 */
class WidgetApiController extends Controller
{
    public function index(WidgetRegistry $registry): JsonResponse
    {
        $widgets = collect($registry->all())
            ->map(fn ($widget) => [
                'type'     => $widget->type(),
                'label'    => $widget->label(),
                'category' => $widget->category(),
                'defaults' => $widget->defaultSettings(),
            ])
            ->values();

        return ApiResponse::success($widgets);
    }
}
