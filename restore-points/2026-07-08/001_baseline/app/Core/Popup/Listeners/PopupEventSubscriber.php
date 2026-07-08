<?php

namespace App\Core\Popup\Listeners;

use App\Core\Popup\Events\PopupCreated;
use App\Core\Popup\Events\PopupDeleted;
use App\Core\Popup\Events\PopupLeadCaptured;
use App\Core\Popup\Events\PopupStatusChanged;
use App\Core\Popup\Events\PopupUpdated;
use App\Core\Popup\Events\PopupViewed;
use App\Models\Popup\PopupActivityLog;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PopupEventSubscriber
{
    public function handlePopupCreated(PopupCreated $event): void
    {
        Cache::forget('popup:dashboard:stats');
    }

    public function handlePopupUpdated(PopupUpdated $event): void
    {
        Cache::forget("popup:{$event->popup->id}");
    }

    public function handlePopupDeleted(PopupDeleted $event): void
    {
        Cache::forget('popup:dashboard:stats');
        Cache::forget("popup:{$event->popup->id}");
    }

    public function handlePopupStatusChanged(PopupStatusChanged $event): void
    {
        Cache::forget('popups:active');
        Cache::forget("popup:{$event->popup->id}");
    }

    public function handlePopupViewed(PopupViewed $event): void
    {
        // Increment view counter
        $event->popup->increment('view_count');
    }

    public function handlePopupLeadCaptured(PopupLeadCaptured $event): void
    {
        // Log lead capture
        Log::info('Popup lead captured', [
            'popup_id' => $event->lead->popup_id,
            'email' => $event->lead->email,
        ]);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            PopupCreated::class => 'handlePopupCreated',
            PopupUpdated::class => 'handlePopupUpdated',
            PopupDeleted::class => 'handlePopupDeleted',
            PopupStatusChanged::class => 'handlePopupStatusChanged',
            PopupViewed::class => 'handlePopupViewed',
            PopupLeadCaptured::class => 'handlePopupLeadCaptured',
        ];
    }
}
