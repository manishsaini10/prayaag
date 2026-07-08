<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Child of Gallery, reached through its parent. Carries no audit/soft-delete of
 * its own (matches the builder's nested row/column/widget models).
 */
class GalleryImage extends Model
{
    use HasUlids;

    protected $fillable = ['gallery_id', 'image', 'caption', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
}
