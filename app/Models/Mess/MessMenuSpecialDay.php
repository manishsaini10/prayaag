<?php

namespace App\Models\Mess;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessMenuSpecialDay extends Model
{
    use HasUlids;

    protected $table = 'mess_menu_special_days';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'items' => 'array',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(MessMenu::class, 'mess_menu_id');
    }
}
