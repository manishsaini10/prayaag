<?php

use App\Providers\AppServiceProvider;
use App\Providers\CoreServiceProvider;
use App\Providers\PopupBuilderServiceProvider;
use App\Providers\ChatbotServiceProvider;

return [
    AppServiceProvider::class,
    CoreServiceProvider::class,
    PopupBuilderServiceProvider::class,
    ChatbotServiceProvider::class,
];
