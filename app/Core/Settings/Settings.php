<?php

namespace App\Core\Settings;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Settings Engine. Use by full name without registering an
 * alias: \App\Core\Settings\Settings::get('site_name').
 *
 * @method static array all()
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static \App\Models\Setting set(string $key, mixed $value, ?string $type = null, ?string $groupSlug = null)
 * @method static void forget(string $key)
 * @method static void flush()
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsManager::class;
    }
}
