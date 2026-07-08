<?php

namespace App\Core\Popup\Events;

use App\Models\Popup\PopupLead;
use Illuminate\Foundation\Events\Dispatchable;

class PopupLeadCaptured
{
    use Dispatchable;

    public function __construct(public readonly PopupLead $lead) {}
}
