<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupRule extends Model
{
    
    protected $table = 'popup_rules';

    protected $fillable = [
        'popup_id', 'type', 'rule_key', 'condition',
        'value', 'extra', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function getValuesAttribute(): array
    {
        $val = $this->value;
        if (!$val) return [];
        $decoded = json_decode($val, true);
        return is_array($decoded) ? $decoded : [$val];
    }
}
