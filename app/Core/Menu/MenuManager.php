<?php

namespace App\Core\Menu;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Convenience accessor for menus by location. Resolve via
 * app(MenuManager::class). Results are cached to minimize DB queries.
 */
class MenuManager
{
    public function location(string $location): ?Menu
    {
        return Cache::remember("theme.menu_location.{$location}", 86400, function () use ($location) {
            return Menu::where('location', $location)->first();
        });
    }

    /** @return Collection */
    public function tree(string $location): Collection
    {
        return Cache::remember("theme.menu_tree.{$location}", 86400, function () use ($location) {
            return $this->location($location)?->tree() ?? collect();
        });
    }

    /** Flush menu cache for a given location or all locations */
    public static function flush(?string $location = null): void
    {
        if ($location) {
            Cache::forget("theme.menu_location.{$location}");
            Cache::forget("theme.menu_tree.{$location}");
        } else {
            Cache::forget('theme.menu_location.primary');
            Cache::forget('theme.menu_tree.primary');
            Cache::forget('theme.menu_location.footer');
            Cache::forget('theme.menu_tree.footer');
            Cache::forget('theme.header');
        }
    }
}
