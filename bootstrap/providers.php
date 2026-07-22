<?php

use App\Providers\AppServiceProvider;
use App\Providers\CoreServiceProvider;
use App\Providers\PopupBuilderServiceProvider;
use App\Providers\ChatbotServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\CoreServiceProvider::class,
    SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
    PopupBuilderServiceProvider::class,
    ChatbotServiceProvider::class,
];
