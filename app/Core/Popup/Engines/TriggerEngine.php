<?php

namespace App\Core\Popup\Engines;

use App\Models\Popup\Popup;
use Illuminate\Support\Collection;

class TriggerEngine
{
    public function getTriggerConfig(Popup $popup): Collection
    {
        return $popup->triggers->map(function ($rule) {
            return [
                'key' => $rule->rule_key,
                'condition' => $rule->condition,
                'value' => $rule->value,
                'extra' => $rule->extra,
            ];
        });
    }

    public function getTriggerType(Popup $popup): string
    {
        $primary = $popup->triggers()->first();
        return $primary?->rule_key ?? 'page_load';
    }

    public function getTriggerDelay(Popup $popup): int
    {
        $delayTrigger = $popup->triggers()
            ->whereIn('rule_key', ['time_delay', 'after_x_seconds'])
            ->first();
        return (int) ($delayTrigger?->value ?? 0);
    }

    public function getScrollPercent(Popup $popup): int
    {
        $scroll = $popup->triggers()->where('rule_key', 'scroll_percent')->first();
        return (int) ($scroll?->value ?? 50);
    }

    public function getClickSelector(Popup $popup): ?string
    {
        $click = $popup->triggers()->where('rule_key', 'click')->first();
        return $click?->value;
    }
}
