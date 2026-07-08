<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A setting value. Stored as text plus a `type` column; read it through
 * castedValue() (or the SettingsManager) to get the native type.
 */
class Setting extends BaseModel
{
    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'group_id');
    }

    public function castedValue(): mixed
    {
        return match ($this->type) {
            'boolean'       => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer'       => (int) $this->value,
            'float'         => (float) $this->value,
            'json', 'array' => json_decode($this->value ?? 'null', true),
            default         => $this->value,
        };
    }
}
