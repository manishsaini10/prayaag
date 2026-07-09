<?php

namespace App\Models\Popup;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupCategory extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'popup_categories';

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function popups(): HasMany
    {
        return $this->hasMany(Popup::class, 'category_id');
    }
}
