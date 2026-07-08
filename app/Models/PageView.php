<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only analytics row. No soft deletes or activity logging (that would
 * be noise and overhead on a high-frequency table) and no updated_at — a view
 * is written once and never changed, so timestamps are disabled and `viewed_at`
 * is set explicitly by the recorder.
 */
class PageView extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = [
        'page_id', 'path', 'referrer', 'ip_hash', 'user_agent', 'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
