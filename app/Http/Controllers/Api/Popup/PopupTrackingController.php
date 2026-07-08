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
            $dto = new AnalyticsDTO(
                popupId: $request->input('popup_id'),
                eventType: $request->input('event_type', 'view'),
                sessionId: $request->input('session_id'),
                ipAddress: $request->ip(),
                userAgent: $request->input('user_agent', $request->userAgent()),
                url: $request->input('url'),
                referrer: $request->input('referrer'),
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
            $formData = $request->input('form_data', []);
            $dto = new LeadDTO(
                popupId: $request->input('popup_id'),
                name: $formData['name'] ?? $request->input('name'),
                email: $formData['email'] ?? $request->input('email'),
                phone: $formData['phone'] ?? $request->input('phone'),
                formData: $formData,
                source: $request->input('url'),
                ipAddress: $request->ip(),
                userAgent: $request->input('user_agent', $request->userAgent()),
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
            $dto = new AnalyticsDTO(
                popupId: $request->input('popup_id'),
                eventType: 'conversion',
                sessionId: $request->input('session_id'),
                ipAddress: $request->ip(),
                userAgent: $request->input('user_agent', $request->userAgent()),
                url: $request->input('url'),
                extraData: $request->input('data', []),
            );

            $this->trackAnalytics->execute($dto);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Popup conversion tracking error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}
