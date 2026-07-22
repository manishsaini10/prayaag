<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Mess\Services\MessMenuService;
use App\Models\Mess\MessMenu;
use App\Models\Mess\MessMenuItem;
use App\Models\Mess\MessMenuSpecialDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessMenuController extends Controller
{
    protected MessMenuService $service;

    public function __construct(MessMenuService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $menus = MessMenu::orderByDesc('effective_from')->get();
        return view('admin.mess-menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.mess-menus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $data['created_by'] = Auth::id();
        $data['is_active'] = false; // Start inactive

        $menu = MessMenu::create($data);

        // Pre-create empty items for all 7 days and 4 meal types
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $meals = ['breakfast', 'lunch', 'snacks', 'dinner'];

        foreach ($days as $day) {
            foreach ($meals as $meal) {
                MessMenuItem::create([
                    'mess_menu_id' => $menu->id,
                    'day_of_week' => $day,
                    'meal_type' => $meal,
                    'items' => [],
                    'notes' => '',
                ]);
            }
        }

        return redirect()->route('admin.mess-menus.edit', $menu->id)
            ->with('success', 'Mess Menu created. Now configure the weekly grid below!');
    }

    public function edit(string $id)
    {
        $menu = MessMenu::findOrFail($id);
        $items = $menu->items;

        // Group by day -> meal
        $grid = [];
        foreach ($items as $item) {
            $grid[$item->day_of_week][$item->meal_type] = [
                'items_str' => implode(', ', $item->items),
                'notes' => $item->notes
            ];
        }

        $specials = $menu->specialDays()->orderBy('date')->get();

        return view('admin.mess-menus.form', compact('menu', 'grid', 'specials'));
    }

    public function update(Request $request, string $id)
    {
        $menu = MessMenu::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date',
            'grid' => 'required|array',
        ]);

        $menu->update([
            'title' => $request->title,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
        ]);

        // Process grid values
        foreach ($request->grid as $day => $meals) {
            foreach ($meals as $meal => $data) {
                // Parse comma-separated list of items into array
                $rawItems = $data['items'] ?? '';
                $itemsArray = array_filter(array_map('trim', explode(',', $rawItems)), fn($val) => $val !== '');

                MessMenuItem::updateOrCreate(
                    ['mess_menu_id' => $menu->id, 'day_of_week' => $day, 'meal_type' => $meal],
                    [
                        'items' => $itemsArray,
                        'notes' => $data['notes'] ?? '',
                    ]
                );
            }
        }

        return redirect()->route('admin.mess-menus.index')
            ->with('success', 'Mess Menu updated successfully.');
    }

    public function toggleActive(string $id)
    {
        $menu = MessMenu::findOrFail($id);

        if (!$menu->is_active) {
            // Deactivate all other menus
            MessMenu::where('id', '!=', $menu->id)->update(['is_active' => false]);
            $menu->update(['is_active' => true]);
        } else {
            $menu->update(['is_active' => false]);
        }

        return redirect()->back()->with('success', 'Mess Menu status updated.');
    }

    public function duplicate(Request $request, string $id)
    {
        $request->validate([
            'effective_from' => 'required|date',
        ]);

        $newMenu = $this->service->duplicateMenu($id, $request->effective_from);

        return redirect()->route('admin.mess-menus.edit', $newMenu->id)
            ->with('success', 'Menu duplicated. Modify the copies below.');
    }

    public function destroy(string $id)
    {
        $menu = MessMenu::findOrFail($id);
        $menu->delete();

        return redirect()->route('admin.mess-menus.index')
            ->with('success', 'Mess Menu deleted.');
    }

    // Special Days Overrides
    public function storeSpecial(Request $request, string $menuId)
    {
        $request->validate([
            'date' => 'required|date',
            'label' => 'nullable|string|max:255',
            'meal_type' => 'required|in:breakfast,lunch,snacks,dinner',
            'items' => 'required|string',
        ]);

        $rawItems = $request->items;
        $itemsArray = array_filter(array_map('trim', explode(',', $rawItems)), fn($val) => $val !== '');

        MessMenuSpecialDay::create([
            'mess_menu_id' => $menuId,
            'date' => $request->date,
            'label' => $request->label,
            'meal_type' => $request->meal_type,
            'items' => $itemsArray,
        ]);

        return redirect()->back()->with('success', 'Special override day added.');
    }

    public function destroySpecial(string $menuId, string $specialId)
    {
        $special = MessMenuSpecialDay::where('mess_menu_id', $menuId)->findOrFail($specialId);
        $special->delete();

        return redirect()->back()->with('success', 'Special override day removed.');
    }
}
