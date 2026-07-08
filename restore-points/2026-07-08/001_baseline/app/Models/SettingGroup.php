<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Global catalog of setting sections (General, SEO, Contact...). Defines
 * the structure of the settings UI; the actual values are rows in the
 * settings table.
 */
class SettingGroup extends BaseModel
{
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'group_id');
    }
}
