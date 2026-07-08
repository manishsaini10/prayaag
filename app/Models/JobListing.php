<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends BaseModel
{
    protected $casts = [
        'closes_at' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_listing_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open')
            ->where(function (Builder $q) {
                $q->whereNull('closes_at')->orWhere('closes_at', '>=', now());
            });
    }
}
