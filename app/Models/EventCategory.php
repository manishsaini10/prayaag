<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EventCategory extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category', 'name');
    }

    /**
     * Get associative array of categories ['Category Name' => 'Category Name']
     * with fallback to standard categories if DB is empty.
     */
    public static function options(): array
    {
        $categories = static::orderBy('sort_order')->orderBy('name')->pluck('name', 'name')->toArray();

        if (empty($categories)) {
            return Event::DEFAULT_CATEGORIES;
        }

        return $categories;
    }
}
