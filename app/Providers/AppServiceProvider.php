<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Enquiry;
use App\Models\JobApplication;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Subscriber;
use App\Core\Seo\IndexNowService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // --- Generate admin notifications from incoming public events ---
        // AdminNotification::record() is self-guarding (rescue), so a missing
        // table or any failure can never break a public form submission.
        Enquiry::created(function (Enquiry $e) {
            AdminNotification::record('enquiry', 'New ' . ($e->type ?: 'enquiry') . ' from ' . ($e->name ?: 'a visitor'), [
                'body' => Str::limit($e->message ?? '', 90),
                'url'  => url('/admin/enquiries'),
                'icon' => 'inbox',
            ]);
        });

        JobApplication::created(function (JobApplication $a) {
            AdminNotification::record('application', 'New job application from ' . ($a->name ?: 'an applicant'), [
                'url'  => url('/admin/applications'),
                'icon' => 'briefcase',
            ]);
        });

        Subscriber::created(function (Subscriber $s) {
            AdminNotification::record('subscriber', 'New subscriber: ' . ($s->email ?? 'unknown'), [
                'level' => 'success',
                'url'   => url('/admin/subscribers'),
                'icon'  => 'envelope',
            ]);
        });

        // --- Keep cached chrome (theme.header) fresh when menus change ---
        $forgetChrome = fn () => Cache::forget('theme.header');
        Menu::saved($forgetChrome);
        Menu::deleted($forgetChrome);
        MenuItem::saved($forgetChrome);
        MenuItem::deleted($forgetChrome);

        // --- SEO: on page change, refresh the image sitemap + ping IndexNow ---
        $onPageChange = function (Page $page) {
            Cache::forget('sitemap.images');
            if ($page->status === 'published' && $page->slug) {
                $url = url($page->slug === 'home' ? '/' : '/' . ltrim($page->slug, '/'));
                rescue(fn () => app(IndexNowService::class)->ping($url), null, false);
            }
        };
        Page::saved($onPageChange);
        Page::deleted(fn () => Cache::forget('sitemap.images'));

        // --- Feed the header bell on every admin page (guarded) ---
        View::composer('admin.layout', function ($view) {
            [$count, $items] = rescue(fn () => [
                AdminNotification::unread()->count(),
                AdminNotification::latest()->limit(8)->get(),
            ], [0, collect()], false);

            $view->with('navUnreadCount', $count)->with('navNotifications', $items);
        });
    }
}
