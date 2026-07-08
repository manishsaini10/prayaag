<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstagramSyncLog extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'api_response'   => 'array',
        'execution_time' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class, 'account_id');
    }
}
