<?php

namespace App\Models\Chatbot\Enterprise;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = [
        'name',
        'token',
        'permissions',
        'rate_limit',
        'expires_at',
        'revoked_at',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    protected $hidden = ['token'];
}
