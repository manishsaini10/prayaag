<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupIntegration extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'popup_integrations';

    protected $fillable = [
        'name', 'provider', 'type',
        'credentials', 'config', 'description',
        'is_active', 'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'config' => 'array',
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PopupIntegrationLog::class, 'integration_id');
    }
}
