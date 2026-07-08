<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * Standalone (not a BaseModel) so writing a log never triggers another
 * log. Only created_at is tracked.
 */
class ActivityLog extends Model
{
    use HasUlids;

    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'properties' => 'array',
    ];
}
