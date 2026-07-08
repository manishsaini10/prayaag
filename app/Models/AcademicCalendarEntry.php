<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class AcademicCalendarEntry extends BaseModel
{
    protected $table = 'academic_calendar';

    protected $casts = [
        'starts_on' => 'date',
        'ends_on'   => 'date',
    ];

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('starts_on', '>=', now()->toDateString());
    }
}
