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

    protected $appends = ['url', 'thumb_url', 'medium_url', 'large_url'];

    public function getUrl(string $size = 'original'): string
    {
        $path = $this->path;
        if ($size !== 'original' && str_starts_with((string) $this->mime_type, 'image/') && $this->mime_type !== 'image/svg+xml') {
            $pathInfo = pathinfo($path);
            $dir = $pathInfo['dirname'] ?? '';
            $dirPrefix = $dir ? $dir . '/' : '';
            $filename = $pathInfo['filename'];
            $ext = $pathInfo['extension'] ?? '';
            if ($ext) {
                $sizePath = $dirPrefix . $filename . '-' . $size . '.' . $ext;
                if (\Illuminate\Support\Facades\Storage::disk($this->disk ?? 'public')->exists($sizePath)) {
                    $path = $sizePath;
                }
            }
        }
        $url = \Illuminate\Support\Facades\Storage::disk($this->disk ?? 'public')->url($path);
        return \App\Core\Media\Services\CdnUrlService::url($url);
    }

    public function getUrlAttribute(): string
    {
        return $this->getUrl('original');
    }

    public function getThumbUrlAttribute(): string
    {
        return $this->getUrl('thumb');
    }

    public function getMediumUrlAttribute(): string
    {
        return $this->getUrl('medium');
    }

    public function getLargeUrlAttribute(): string
    {
        return $this->getUrl('large');
    }

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
