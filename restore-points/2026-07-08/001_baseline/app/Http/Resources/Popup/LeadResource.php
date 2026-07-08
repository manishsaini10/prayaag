<?php

namespace App\Http\Resources\Popup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'popup_id' => $this->popup_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'form_data' => $this->form_data,
            'status' => $this->status,
            'source' => $this->source,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'tags' => $this->tags,
            'assigned_to' => $this->assigned_to,
            'converted_at' => $this->converted_at,
            'created_at' => $this->created_at,
        ];
    }
}
