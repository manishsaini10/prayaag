<?php

namespace App\Core\Popup\Actions;

use App\Core\Popup\DTOs\PopupDTO;
use App\Core\Popup\Services\PopupService;
use App\Models\Popup\Popup;

class CreatePopupAction
{
    public function __construct(private readonly PopupService $popupService) {}

    public function execute(array $data, ?string $templateId = null): Popup
    {
        $dto = PopupDTO::fromArray($data);
        return $this->popupService->create($dto, $templateId);
    }
}
