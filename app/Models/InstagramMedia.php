<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstagramMedia extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'likes'      => 'integer',
        'comments'   => 'integer',
        'is_cached'  => 'boolean',
        'children'   => 'array',
        'raw'        => 'array',
        'posted_at'  => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('media_id');
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('media_type', $type);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('media_type', 'IMAGE');
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->whereIn('media_type', ['VIDEO', 'REEL']);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class, 'instagram_account_id');
    }
}
