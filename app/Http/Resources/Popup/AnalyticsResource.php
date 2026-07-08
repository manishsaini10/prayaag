<?php

namespace App\Http\Resources\Popup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'popup_id' => $this->popup_id,
            'variation_id' => $this->variation_id,
            'event_type' => $this->event_type,
            'session_id' => $this->session_id,
            'url' => $this->url,
            'referrer' => $this->referrer,
            'country' => $this->country,
            'device_type' => $this->device_type,
            'browser' => $this->browser,
            'os' => $this->os,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'occurred_at' => $this->occurred_at,
        ];
    }
}
