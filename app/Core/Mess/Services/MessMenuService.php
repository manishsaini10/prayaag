<?php

namespace App\Core\Mess\Services;

use App\Models\Mess\MessMenu;
use App\Models\Mess\MessMenuItem;
use App\Models\Mess\MessMenuSpecialDay;
use Illuminate\Support\Carbon;

class MessMenuService
{
    /**
     * Get the active weekly menu grouped by day and meal type.
     */
    public function getActiveMenuGrouped(): array
    {
        $activeMenu = MessMenu::active()->first();
        if (!$activeMenu) {
            return [];
        }

        $items = $activeMenu->items()->orderBy('sort_order')->get();
        
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
                'items' => $item->items,
                'notes' => $item->notes
            ];
        }

        return [
            'menu' => $activeMenu,
            'grouped' => $grouped
        ];
    }

    /**
     * Get special overrides for a given date.
     */
    public function getSpecialOverrideForDate(string $date): array
    {
        $parsedDate = Carbon::parse($date)->format('Y-m-d');
        $specials = MessMenuSpecialDay::where('date', $parsedDate)->get();

        $grouped = [];
        foreach ($specials as $special) {
            $grouped[$special->meal_type] = [
                'label' => $special->label,
                'items' => $special->items
            ];
        }

        return $grouped;
    }

    /**
     * Duplicate a template menu's daily schedules into a new menu block.
     */
    public function duplicateMenu(string $sourceMenuId, string $newEffectiveFromDate): MessMenu
    {
        $sourceMenu = MessMenu::findOrFail($sourceMenuId);

        // Create new menu
        $newMenu = MessMenu::create([
            'title' => $sourceMenu->title . ' (Copy)',
            'effective_from' => Carbon::parse($newEffectiveFromDate)->format('Y-m-d'),
            'is_active' => false,
        ]);

        // Copy daily items
        foreach ($sourceMenu->items as $item) {
            MessMenuItem::create([
                'mess_menu_id' => $newMenu->id,
                'day_of_week' => $item->day_of_week,
                'meal_type' => $item->meal_type,
                'items' => $item->items,
                'notes' => $item->notes,
                'sort_order' => $item->sort_order,
            ]);
        }

        // Copy special days
        foreach ($sourceMenu->specialDays as $special) {
            MessMenuSpecialDay::create([
                'mess_menu_id' => $newMenu->id,
                'date' => $special->date,
                'label' => $special->label,
                'meal_type' => $special->meal_type,
                'items' => $special->items,
            ]);
        }

        return $newMenu;
    }
}
