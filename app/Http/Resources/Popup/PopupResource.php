<?php

namespace App\Http\Resources\Popup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'status' => $this->status,
            'category' => $this->whenLoaded('category'),
            'template_id' => $this->template_id,
            'structure' => $this->structure,
            'settings' => $this->settings,
            'design' => $this->design,
            'styles' => $this->styles,
            'custom_css' => $this->custom_css,
            'custom_js' => $this->custom_js,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'frequency_type' => $this->frequency_type,
            'is_ab_test' => $this->is_ab_test,
            'ab_test' => $this->whenLoaded('abTest'),
            'rules' => PopupRuleResource::collection($this->whenLoaded('rules')),
            'triggers' => PopupRuleResource::collection($this->whenLoaded('triggers')),
            'display_rules' => PopupRuleResource::collection($this->whenLoaded('displayRules')),
            'targeting_rules' => PopupRuleResource::collection($this->whenLoaded('targetingRules')),
            'view_count' => $this->view_count,
            'click_count' => $this->click_count,
            'conversion_count' => $this->conversion_count,
            'conversion_rate' => $this->conversion_rate,
            'priority' => $this->priority,
            'meta' => $this->meta,
            'noindex' => $this->noindex,
            'is_template' => $this->is_template,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
