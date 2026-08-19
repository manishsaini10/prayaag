<?php

namespace App\Core\Mess\Services;

use App\Models\Mess\MessMenu;
use App\Models\Mess\MessMenuItem;
use App\Models\Mess\MessMenuSpecialDay;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class MessMenuService
{
    /**
     * Get the active weekly menu grouped by day and meal type (cached).
     */
    public function getActiveMenuGrouped(): array
    {
        return Cache::remember('mess_menu:active_grouped', 3600, function () {
            $activeMenu = MessMenu::active()->with(['items', 'specialDays'])->first();
            if (!$activeMenu) {
                return [];
            }

            $items = $activeMenu->items()->orderBy('sort_order')->get();
            $specialDays = $activeMenu->specialDays;

            $grouped = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $meals = ['breakfast', 'lunch', 'snacks', 'dinner'];

            // Initialize structured array
            foreach ($days as $day) {
                foreach ($meals as $meal) {
                    $grouped[$day][$meal] = [
                        'items' => [],
                        'notes' => ''
                    ];
                }
            }

            // Fill data
            foreach ($items as $item) {
                $grouped[$item->day_of_week][$item->meal_type] = [
                    'items' => $item->items ?? [],
                    'notes' => $item->notes ?? ''
                ];
            }

            // Build complete schedule from effective_from to effective_to
            $schedule = [];
            $startDate = $activeMenu->effective_from->copy();
            $endDate = $activeMenu->effective_to 
                ? $activeMenu->effective_to->copy() 
                : $startDate->copy()->addDays(6);

            $specialsByDate = $specialDays->groupBy(function ($sd) {
                return $sd->date ? $sd->date->format('Y-m-d') : '';
            });

            $hasExplicitDates = $specialDays->isNotEmpty();

            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $dateStr = $current->format('Y-m-d');
                $dayKey = strtolower($current->format('l'));

                $daySpecials = $specialsByDate->get($dateStr);
                $lunchSpecial = $daySpecials ? $daySpecials->firstWhere('meal_type', 'lunch') : null;

                if ($lunchSpecial) {
                    $dishes = $lunchSpecial->items ?? [];
                    $notes = $lunchSpecial->notes ?? ($lunchSpecial->label ?? '');
                } elseif ($hasExplicitDates) {
                    // When specific date overrides are provided for the menu period, non-listed dates are off/unscheduled
                    $dishes = [];
                    $notes = '';
                } else {
                    $dishes = $grouped[$dayKey]['lunch']['items'] ?? [];
                    $notes = $grouped[$dayKey]['lunch']['notes'] ?? '';
                }

                $schedule[] = [
                    'date' => $current->copy(),
                    'date_str' => $dateStr,
                    'day_key' => $dayKey,
                    'day_name' => ucfirst($dayKey),
                    'dishes' => $dishes,
                    'notes' => $notes,
                    'has_lunch' => !empty($dishes),
                ];

                $current->addDay();
            }

            return [
                'menu' => $activeMenu,
                'grouped' => $grouped,
                'schedule' => $schedule,
            ];
        });
    }

    /**
     * Get special overrides for a given date (cached).
     */
    public function getSpecialOverrideForDate(string $date): array
    {
        $parsedDate = Carbon::parse($date)->format('Y-m-d');

        return Cache::remember("mess_menu:specials:{$parsedDate}", 1800, function () use ($parsedDate) {
            $specials = MessMenuSpecialDay::where('date', $parsedDate)->get();

            $grouped = [];
            foreach ($specials as $special) {
                $grouped[$special->meal_type] = [
                    'items' => $special->items,
                    'notes' => $special->notes ?? $special->label ?? '',
                    'is_cancelled' => $special->is_cancelled ?? false
                ];
            }

            return $grouped;
        });
    }

    /**
     * Invalidate mess menu cache keys. Called when mess menus or special days are modified.
     */
    public static function flush(): void
    {
        Cache::forget('mess_menu:active_grouped');
    }
}
