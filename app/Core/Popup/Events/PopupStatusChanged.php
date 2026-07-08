<?php

namespace App\Core\Popup\Events;

use App\Models\Popup\Popup;
use Illuminate\Foundation\Events\Dispatchable;

class PopupStatusChanged
{
    use Dispatchable;

    public function __construct(
        public readonly Popup $popup,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {}
}
