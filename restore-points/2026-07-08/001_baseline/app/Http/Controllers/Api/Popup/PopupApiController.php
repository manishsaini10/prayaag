<?php

namespace App\Http\Controllers\Api\Popup;

use App\Core\Popup\Actions\CreatePopupAction;
use App\Core\Popup\Actions\DuplicatePopupAction;
use App\Core\Popup\Actions\PublishPopupAction;
use App\Core\Popup\DTOs\PopupDTO;
use App\Core\Popup\Engines\RenderingEngine;
use App\Core\Popup\Repositories\PopupRepository;
use App\Core\Popup\Services\AnalyticsService;
use App\Core\Popup\Services\PopupService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Popup\PopupResource;
use App\Models\Popup\Popup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PopupApiController extends Controller
{
    public function __construct(
        private readonly PopupRepository $repository,
        private readonly PopupService $popupService,
        private readonly AnalyticsService $analyticsService,
        private readonly RenderingEngine $renderingEngine,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $popups = Popup::query()
            ->with('category')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy($request->sort ?? 'updated_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return PopupResource::collection($popups);
    }

    public function show(Popup $popup): PopupResource
    {
        $popup->load(['category', 'rules', 'triggers', 'displayRules', 'targetingRules', 'schedules', 'abTest.variants']);
        return new PopupResource($popup);
    }

    public function store(Request $request, CreatePopupAction $action): PopupResource
    {
        $validated = $request->validate([
            'title' => 'required|min:2|max:255',
            'type' => 'required|string',
            'structure' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
        ]);

        $popup = $action->execute($validated, $request->template_id);
        return new PopupResource($popup);
    }

    public function update(Request $request, Popup $popup): PopupResource
    {
        $validated = $request->validate([
            'title' => 'sometimes|min:2|max:255',
            'type' => 'sometimes|string',
            'structure' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
        ]);

        $dto = PopupDTO::fromArray(array_merge($popup->toArray(), $validated));
        $popup = $this->popupService->update($popup, $dto);
        return new PopupResource($popup);
    }

    public function destroy(Popup $popup): JsonResponse
    {
        $this->popupService->delete($popup);
        return response()->json(['message' => 'Popup deleted']);
    }

    public function duplicate(Popup $popup, DuplicatePopupAction $action): PopupResource
    {
        $clone = $action->execute($popup);
        return new PopupResource($clone);
    }

    public function publish(Popup $popup, PublishPopupAction $action): PopupResource
    {
        $popup = $action->execute($popup);
        return new PopupResource($popup);
    }

    public function unpublish(Popup $popup): PopupResource
    {
        $popup = $this->popupService->unpublish($popup);
        return new PopupResource($popup);
    }

    public function active(): AnonymousResourceCollection
    {
        $popups = $this->repository->getAllActive();
        return PopupResource::collection($popups);
    }

    public function render(Popup $popup): JsonResponse
    {
        if (! $popup->isVisible()) {
            return response()->json(['error' => 'Popup is not visible'], 404);
        }
        $html = $this->renderingEngine->render($popup);
        return response()->json(['html' => $html, 'popup' => new PopupResource($popup)]);
    }

    public function analytics(Popup $popup): JsonResponse
    {
        $stats = $this->analyticsService->getPopupStats($popup->id, request('period', '30d'));
        $daily = $this->analyticsService->getDailyStats($popup->id, 'view', request('period', '30d'));
        $devices = $this->analyticsService->getDeviceBreakdown($popup->id, request('period', '30d'));
        $countries = $this->analyticsService->getCountryBreakdown($popup->id, request('period', '30d'));

        return response()->json([
            'stats' => $stats,
            'daily' => $daily,
            'devices' => $devices,
            'countries' => $countries,
        ]);
    }

    public function leads(Popup $popup): AnonymousResourceCollection
    {
        $leads = $popup->leads()
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 20));

        return \App\Http\Resources\Popup\LeadResource::collection($leads);
    }
}
