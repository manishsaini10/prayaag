<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = AdminNotification::latest()->paginate(30);
        $unread = AdminNotification::unread()->count();

        return view('admin.notifications.index', compact('notifications', 'unread'));
    }

    public function markAllRead(): RedirectResponse
    {
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }

    public function markRead(AdminNotification $notification): RedirectResponse
    {
        if ($notification->isUnread()) {
            $notification->update(['read_at' => now()]);
        }

        // Bounce to the linked record if there is one, else back to the list.
        return $notification->url
            ? redirect($notification->url)
            : back();
    }
}
