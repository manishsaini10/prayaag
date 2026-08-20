<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Event extends BaseModel
{
    public const DEFAULT_CATEGORIES = [
        'Academic'        => 'Academic',
        'Sports'          => 'Sports & Athletics',
        'Cultural'        => 'Cultural & Arts',
        'Celebration'     => 'Celebration & Festival',
        'Exhibition'      => 'Exhibition & Science',
        'Workshop'        => 'Workshop & Seminar',
        'Annual Function' => 'Annual Function & Sports Day',
        'Competition'     => 'Competition & Olympiad',
        'Holiday'         => 'Holiday & Break',
        'Examination'     => 'Examination & Assessment',
        'General'         => 'General',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public static function categories(): array
    {
        try {
            return EventCategory::options();
        } catch (\Throwable $e) {
            return self::DEFAULT_CATEGORIES;
        }
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now());
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (empty($category) || strtolower($category) === 'all') {
            return $query;
        }

        return $query->where('category', $category);
    }

    public function scopeCategories(Builder $query, array $categories): Builder
    {
        $filtered = array_filter($categories, fn ($c) => !empty($c) && strtolower($c) !== 'all');

        if (empty($filtered)) {
            return $query;
        }

        return $query->whereIn('category', $filtered);
    }
}
