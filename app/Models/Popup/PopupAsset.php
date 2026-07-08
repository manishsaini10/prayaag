<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupAsset extends Model
{
    use SoftDeletes;

    protected $table = 'popup_assets';

    protected $fillable = [
        'popup_id', 'name', 'file_name', 'path',
        'disk', 'mime_type', 'size', 'type', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'size' => 'integer',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function getUrlAttribute(): string
    {
        return asset("storage/{$this->path}");
    }
}
