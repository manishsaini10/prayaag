<?php

namespace App\View\Components\Popup;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PopupBuilderAssets extends Component
{
    public function render(): View
    {
        return view('components.popup.assets');
    }
}
