<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * A captured submission: contact message, admission lead, or general enquiry.
 * Represents the blueprint's leads/enquiries/contacts, distinguished by `type`.
 */
class Enquiry extends BaseModel
{
    protected $table = 'enquiries';

    protected $casts = [
        'meta' => 'array',
    ];

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }
}
