<?php

namespace App\Core\Popup\Actions;

use App\Core\Popup\Services\PopupService;
use App\Models\Popup\Popup;

class PublishPopupAction
{
    public function __construct(private readonly PopupService $popupService) {}

    public function execute(Popup $popup): Popup
    {
        return $this->popupService->publish($popup);
    }
}
