<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends BaseModel
{
    protected $table = 'email_templates';

    protected $casts = [
        'available_placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    public function revisions(): HasMany
    {
        return $this->hasMany(EmailTemplateRevision::class, 'email_template_id')->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }
}
