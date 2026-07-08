<?php

namespace App\Core\Menu;

use App\Models\Menu;
use Illuminate\Support\Collection;

/**
 * Convenience accessor for menus by location. Resolve via
 * app(MenuManager::class).
 */
class MenuManager
{
    public function location(string $location): ?Menu
    {
        return Menu::where('location', $location)->first();
    }

    /** @return Collection */
    public function tree(string $location): Collection
    {
        return $this->location($location)?->tree() ?? collect();
    }
}
