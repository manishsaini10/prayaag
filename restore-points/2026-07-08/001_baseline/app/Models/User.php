<?php

namespace App\Models;

use App\Core\Concerns\HasRoles;
use App\Core\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Tenant-scoped user. Extends Authenticatable (not BaseModel) but wears
 * the same conventions via traits: ULID keys, soft deletes, auditing,
 * and RBAC.
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasUlids;
    use SoftDeletes;
    use RecordsActivity;
    use HasRoles;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
