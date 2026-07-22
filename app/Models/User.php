<?php

namespace App\Models;

use App\Core\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
            'two_factor_recovery_codes' => 'array',
        ];
    }

    /**
     * Generate and store recovery codes.
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = sprintf('%04x-%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        }
        $this->update([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ]);
        return $codes;
    }

    /**
     * Verify a recovery code and consume it.
     */
    public function consumeRecoveryCode(string $code): bool
    {
        $stored = [];
        if ($this->two_factor_recovery_codes) {
            try {
                $stored = json_decode(decrypt($this->two_factor_recovery_codes), true) ?: [];
            } catch (\Exception $e) {
                $stored = [];
            }
        }
        if (($key = array_search($code, $stored)) !== false) {
            unset($stored[$key]);
            $this->update([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($stored))),
            ]);
            return true;
        }
        return false;
    }
}
