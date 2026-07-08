<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupIntegrationLog extends Model
{
    
    protected $table = 'popup_integration_logs';

    protected $fillable = [
        'integration_id', 'popup_id', 'event_type',
        'status', 'request', 'response',
        'status_code', 'error_message',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(PopupIntegration::class);
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }
}
