<?php

namespace App\Core\Popup\Actions;

use App\Core\Popup\DTOs\LeadDTO;
use App\Core\Popup\Events\PopupLeadCaptured;
use App\Models\Popup\PopupLead;

class CaptureLeadAction
{
    public function execute(LeadDTO $dto): PopupLead
    {
        $lead = PopupLead::create($dto->toArray());
        event(new PopupLeadCaptured($lead));
        return $lead;
    }
}
