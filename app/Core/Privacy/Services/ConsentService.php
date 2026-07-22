<?php

namespace App\Core\Privacy\Services;

use App\Core\Privacy\Models\ConsentLog;
use Illuminate\Database\Eloquent\Model;

class ConsentService
{
    public static function log(?string $email, string $consentType, string $consentText, ?Model $consentable = null): ConsentLog
    {
        return ConsentLog::create([
            'email'            => $email,
            'consentable_type' => $consentable ? get_class($consentable) : null,
            'consentable_id'   => $consentable ? $consentable->getKey() : null,
            'consent_type'     => $consentType,
            'consent_text'     => $consentText,
            'ip_address'       => request()->ip(),
            'given_at'         => now(),
        ]);
    }
}
