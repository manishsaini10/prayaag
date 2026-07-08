<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Media record. The binary lives on a storage disk; this row holds the
 * path and metadata.
 */
class Media extends BaseModel
{
    protected $table = 'media';

    protected $casts = [
        'size'   => 'integer',
        'width'  => 'integer',
        'height' => 'integer',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('original_name', 'like', "%{$term}%")
                ->orWhere('filename', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%");
        });
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('mime_type', 'like', $type . '/%');
    }
}
