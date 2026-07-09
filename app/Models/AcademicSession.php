<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSession extends Model
{
    protected $table = 'academic_sessions';

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(AcademicTerm::class, 'session_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(AcademicCalendarEntry::class, 'session_id');
    }
}
