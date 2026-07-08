<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Folder tree for the media library. `path` is the slash-joined slug chain,
 * maintained on create.
 */
class MediaFolder extends BaseModel
{
    protected static function booted(): void
    {
        static::creating(function (MediaFolder $folder) {
            if (empty($folder->slug)) {
                $folder->slug = Str::slug($folder->name);
            }

            if (empty($folder->path)) {
                $parentPath = $folder->parent_id
                    ? optional(self::find($folder->parent_id))->path
                    : null;

                $folder->path = trim(($parentPath ? $parentPath . '/' : '') . $folder->slug, '/');
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}
