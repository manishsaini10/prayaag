<?php

namespace App\Core\Privacy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DataPrivacyRequest extends Model
{
    use HasUlids;

    protected $table = 'data_privacy_requests';

    protected $guarded = ['id'];

    protected $casts = [
        'verified_at'  => 'datetime',
        'processed_at' => 'datetime',
    ];
}
