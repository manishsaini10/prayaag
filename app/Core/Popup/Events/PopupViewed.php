<?php

namespace App\Core\Popup\Events;

use App\Models\Popup\Popup;
use Illuminate\Foundation\Events\Dispatchable;

class PopupViewed
{
    use Dispatchable;

    public function __construct(
        public readonly Popup $popup,
        public readonly array $context = [],
    ) {}
}
