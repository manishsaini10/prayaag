<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'popup_templates';

    protected $fillable = [
        'name', 'slug', 'description', 'type', 'category',
        'thumbnail', 'structure', 'settings', 'styles',
        'is_premium', 'is_built_in', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'structure' => 'array',
            'settings' => 'array',
            'styles' => 'array',
            'is_premium' => 'boolean',
            'is_built_in' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function popups(): HasMany
    {
        return $this->hasMany(Popup::class, 'template_id');
    }
}
