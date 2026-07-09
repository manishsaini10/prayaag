<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $guarded = ['id'];

    public function entries(): HasMany
    {
        return $this->hasMany(AcademicCalendarEntry::class, 'class_id');
    }
}
