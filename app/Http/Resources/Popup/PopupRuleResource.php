<?php

namespace App\Http\Resources\Popup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'rule_key' => $this->rule_key,
            'condition' => $this->condition,
            'value' => $this->value,
            'extra' => $this->extra,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
