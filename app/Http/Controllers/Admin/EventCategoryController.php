<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of categories or return JSON list.
     */
    public function index(Request $request): JsonResponse|View
    {
        $categories = EventCategory::withCount('events')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'categories' => $categories,
            ]);
        }

        return view('admin.events.categories', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:event_categories,name',
            'color' => 'nullable|string|max:50',
        ]);

        $category = EventCategory::create([
            'name'       => trim($request->name),
            'slug'       => Str::slug($request->name),
            'color'      => $request->color ?: '#0b2545',
            'sort_order' => EventCategory::count() + 1,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Category created successfully.',
                'category' => $category,
            ]);
        }

        return back()->with('status', 'Category "' . $category->name . '" created successfully.');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $category = EventCategory::findOrFail($id);
        $oldName  = $category->name;

        $request->validate([
            'name'  => 'required|string|max:100|unique:event_categories,name,' . $id,
            'color' => 'nullable|string|max:50',
        ]);

        $newName = trim($request->name);

        $category->update([
            'name'  => $newName,
            'slug'  => Str::slug($newName),
            'color' => $request->color ?: $category->color,
        ]);

        // If name changed, update associated events as well
        if ($oldName !== $newName) {
            Event::where('category', $oldName)->update(['category' => $newName]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Category updated successfully.',
                'category' => $category,
            ]);
        }

        return back()->with('status', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $category = EventCategory::findOrFail($id);
        $name     = $category->name;

        // Reassign existing events in this category to 'General'
        Event::where('category', $name)->update(['category' => 'General']);

        $category->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully. Associated events reassigned to General.',
            ]);
        }

        return back()->with('status', 'Category "' . $name . '" deleted successfully.');
    }
}
