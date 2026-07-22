<?php

namespace App\Core\Privacy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ConsentLog extends Model
{
    use HasUlids;

    protected $table = 'consent_logs';

    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'given_at'     => 'datetime',
        'withdrawn_at' => 'datetime',
    ];
}
