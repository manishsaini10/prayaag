<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Navigation menu manager. Menus are simple records (name/slug/location); the
 * real work is managing their nested items — each item links to a Page or a
 * custom URL, can nest under a parent, and carries a sort order and target.
 */
class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::withCount('items')->orderBy('name')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'location' => 'nullable|string|max:100',
        ]);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $menu = Menu::create($data);

        return redirect()
            ->route('admin.menus.show', $menu)
            ->with('status', 'Menu created — add some items below.');
    }

    public function show(Menu $menu): View
    {
        $menu->load('items.page');

        // Flatten the tree with a depth marker so the view can indent children.
        $flat = [];
        $walk = function ($parentId, $depth) use (&$walk, &$flat, $menu) {
            foreach ($menu->items->where('parent_id', $parentId)->sortBy('sort_order') as $item) {
                $flat[] = ['item' => $item, 'depth' => $depth];
                $walk($item->id, $depth + 1);
            }
        };
        $walk(null, 0);

        $pages = Page::orderBy('title')->get(['id', 'title', 'slug']);

        return view('admin.menus.show', [
            'menu'  => $menu,
            'flat'  => $flat,
            'pages' => $pages,
            'items' => $menu->items->sortBy('sort_order'),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'slug'     => 'required|string|max:255|unique:menus,slug,' . $menu->id . ',id',
            'location' => 'nullable|string|max:100',
        ]);

        $menu->update($data);

        return back()->with('status', 'Menu settings saved.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('status', 'Menu deleted.');
    }

    public function storeItem(Request $request, Menu $menu): RedirectResponse
    {
        $menu->items()->create($this->validatedItem($request, $menu));

        return back()->with('status', 'Item added.');
    }

    public function updateItem(Request $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $item->update($this->validatedItem($request, $menu, $item));

        return back()->with('status', 'Item updated.');
    }

    public function destroyItem(Menu $menu, MenuItem $item): RedirectResponse
    {
        abort_unless($item->menu_id === $menu->id, 404);

        $item->delete(); // children cascade via FK

        return back()->with('status', 'Item removed.');
    }

    /* ----------------------------------------------------------------- */

    /** @return array<string, mixed> */
    protected function validatedItem(Request $request, Menu $menu, ?MenuItem $item = null): array
    {
        $data = $request->validate([
            'label'      => 'required|string|max:255',
            'type'       => 'required|in:url,page,custom',
            'url'        => 'nullable|string|max:2048',
            'page_id'    => 'nullable|exists:pages,id',
            'target'     => 'required|in:_self,_blank',
            'parent_id'  => 'nullable|exists:menu_items,id',
            'sort_order' => 'nullable|integer',
        ]);

        // An item can't be its own parent, and the parent must belong to this menu.
        if (! empty($data['parent_id'])) {
            $parent = MenuItem::find($data['parent_id']);
            if (! $parent || $parent->menu_id !== $menu->id || ($item && $parent->id === $item->id)) {
                $data['parent_id'] = null;
            }
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        if ($data['type'] === 'page') {
            $data['url'] = null;
        } else {
            $data['page_id'] = null;
        }

        return $data;
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'menu';
        $slug = $base;
        while (Menu::where('slug', $slug)->exists()) {
            $slug = $base . '-' . Str::lower(Str::random(4));
        }

        return $slug;
    }
}
