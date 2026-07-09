<?php

namespace App\Models\Popup;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupLead extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'popup_leads';

    protected $fillable = [
        'popup_id', 'name', 'email', 'phone',
        'form_data', 'status', 'notes',
        'source', 'utm_source', 'utm_medium', 'utm_campaign',
        'ip_address', 'user_agent', 'country',
        'tags', 'assigned_to', 'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'tags' => 'array',
            'converted_at' => 'datetime',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}
