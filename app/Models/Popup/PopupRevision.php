<?php

namespace App\Models\Popup;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupRevision extends Model
{
    use HasUlids;

    protected $table = 'popup_revisions';

    protected $fillable = [
        'popup_id', 'version', 'note',
        'structure', 'settings', 'design',
        'diff', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'structure' => 'array',
            'settings' => 'array',
            'design' => 'array',
            'diff' => 'array',
            'version' => 'integer',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
