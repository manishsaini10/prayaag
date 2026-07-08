<?php

namespace App\Core\Popup\Engines;

use App\Models\Popup\Popup;
use App\Models\Popup\PopupAbTest;
use App\Models\Popup\PopupAbTestVariant;
use Illuminate\Support\Facades\DB;

class AbTestEngine
{
    public function getVariant(Popup $popup): ?PopupAbTestVariant
    {
        if (! $popup->is_ab_test || ! $popup->abTest || ! $popup->abTest->isRunning()) {
            return null;
        }

        $test = $popup->abTest;
        $variants = $test->variants()->where('variant_type', 'variant')->get();
        if ($variants->isEmpty()) return null;

        $split = $test->traffic_split;
        $roll = rand(1, 100);

        if ($roll <= $split) {
            return $test->originals()->first();
        }

        return $variants->random();
    }

    public function determineWinner(PopupAbTest $test): ?PopupAbTestVariant
    {
        if (! $test->canDetermineWinner()) return null;

        $variants = $test->variants;
        $best = null;
        $bestRate = -1;

        foreach ($variants as $variant) {
            $rate = $variant->conversion_rate;
            if ($rate > $bestRate) {
                $bestRate = $rate;
                $best = $variant;
            }
        }

        if ($best && $bestRate > 0) {
            $test->update([
                'winner_id' => $best->id,
                'status' => 'completed',
                'ended_at' => now(),
                'results' => $variants->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'views' => $v->view_count,
                    'conversions' => $v->conversion_count,
                    'rate' => $v->conversion_rate,
                ])->toArray(),
            ]);

            // Auto-winner: apply winning variant to main popup
            if ($test->auto_winner && $best->variant_type === 'variant') {
                $popup = $test->popups()->first();
                if ($popup) {
                    $popup->update([
                        'structure' => $best->structure,
                        'settings' => $best->settings,
                        'design' => $best->design,
                    ]);
                }
            }
        }

        return $best;
    }
}
