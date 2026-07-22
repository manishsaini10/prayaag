<?php

namespace App\Models\Mess;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class MessMenu extends BaseModel
{
    protected $table = 'mess_menus';

    protected $fillable = ['title', 'effective_from', 'effective_to', 'is_active', 'created_by'];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MessMenuItem::class, 'mess_menu_id');
    }

    public function specialDays(): HasMany
    {
        return $this->hasMany(MessMenuSpecialDay::class, 'mess_menu_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
