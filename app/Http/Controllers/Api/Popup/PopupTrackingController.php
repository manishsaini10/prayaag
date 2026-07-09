<?php

namespace App\Http\Controllers\Api\Popup;

use App\Core\Popup\Actions\CaptureLeadAction;
use App\Core\Popup\Actions\TrackAnalyticsAction;
use App\Core\Popup\DTOs\AnalyticsDTO;
use App\Core\Popup\DTOs\LeadDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PopupTrackingController extends Controller
{
    public function __construct(
        private readonly TrackAnalyticsAction $trackAnalytics,
        private readonly CaptureLeadAction $captureLead,
    ) {}

    public function track(Request $request): JsonResponse
    {
        try {
            $payload = $this->payload($request);

            $dto = new AnalyticsDTO(
                popupId: $payload['popup_id'] ?? '',
                eventType: $payload['event_type'] ?? 'view',
                sessionId: $payload['session_id'] ?? null,
                ipAddress: $request->ip(),
                userAgent: $payload['user_agent'] ?? $request->userAgent(),
                url: $payload['url'] ?? null,
                referrer: $payload['referrer'] ?? null,
                extraData: [
                    'device_type' => $payload['device_type'] ?? null,
                ],
            );

            $this->trackAnalytics->execute($dto);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Popup tracking error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function lead(Request $request): JsonResponse
    {
        try {
            $payload = $this->payload($request);
            $formData = $payload['form_data'] ?? [];
            $dto = new LeadDTO(
                popupId: $payload['popup_id'] ?? '',
                name: $formData['name'] ?? ($payload['name'] ?? null),
                email: $formData['email'] ?? ($payload['email'] ?? null),
                phone: $formData['phone'] ?? ($payload['phone'] ?? null),
                formData: $formData,
                source: $payload['url'] ?? null,
                ipAddress: $request->ip(),
                userAgent: $payload['user_agent'] ?? $request->userAgent(),
            );

            $this->captureLead->execute($dto);
            return response()->json(['success' => true, 'message' => 'Lead captured']);
        } catch (\Exception $e) {
            Log::error('Popup lead capture error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error capturing lead'], 500);
        }
    }

    public function conversion(Request $request): JsonResponse
    {
        try {
            $payload = $this->payload($request);

            $dto = new AnalyticsDTO(
                popupId: $payload['popup_id'] ?? '',
                eventType: 'conversion',
                sessionId: $payload['session_id'] ?? null,
                ipAddress: $request->ip(),
                userAgent: $payload['user_agent'] ?? $request->userAgent(),
                url: $payload['url'] ?? null,
                extraData: $payload['data'] ?? [],
            );

            $this->trackAnalytics->execute($dto);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Popup conversion tracking error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    private function payload(Request $request): array
    {
        $payload = $request->all();

        if ($payload !== []) {
            return $payload;
        }

        $decoded = json_decode($request->getContent(), true);

        return is_array($decoded) ? $decoded : [];
    }
}
