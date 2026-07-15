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

        // --- Generate admin notifications for login, page, post, event, notice, achievement updates ---
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function (\Illuminate\Auth\Events\Login $event) {
            $user = $event->user;
            $ip = request()->ip() ?? 'unknown';
            AdminNotification::record('login', "User {$user->name} logged in successfully", [
                'level' => 'success',
                'body'  => "IP: {$ip} | Time: " . now()->format('M j, g:i a'),
                'url'   => url('/admin'),
                'icon'  => 'users',
            ]);
        });

        Page::created(function (Page $page) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('page', "New page created: '{$page->title}'", [
                'body' => "Created by {$adminName}",
                'url'  => url('/admin/pages'),
                'icon' => 'document',
            ]);
        });

        Page::updated(function (Page $page) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('page', "Page updated: '{$page->title}'", [
                'body' => "Updated by {$adminName}",
                'url'  => url('/admin/pages'),
                'icon' => 'pencil',
            ]);
        });

        \App\Models\Post::created(function (\App\Models\Post $post) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('post', "New post published: '{$post->title}'", [
                'body' => "Published by {$adminName}",
                'url'  => url('/admin/m/posts'),
                'icon' => 'megaphone',
            ]);
        });

        \App\Models\Post::updated(function (\App\Models\Post $post) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('post', "Post updated: '{$post->title}'", [
                'body' => "Updated by {$adminName}",
                'url'  => url('/admin/m/posts'),
                'icon' => 'pencil-square',
            ]);
        });

        \App\Models\Event::created(function (\App\Models\Event $event) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('event', "New event scheduled: '{$event->title}'", [
                'body' => "Scheduled by {$adminName}",
                'url'  => url('/admin/m/events'),
                'icon' => 'calendar',
            ]);
        });

        \App\Models\Event::updated(function (\App\Models\Event $event) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('event', "Event updated: '{$event->title}'", [
                'body' => "Updated by {$adminName}",
                'url'  => url('/admin/m/events'),
                'icon' => 'calendar',
            ]);
        });

        \App\Models\Notice::created(function (\App\Models\Notice $notice) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('notice', "New notice posted: '{$notice->title}'", [
                'body' => "Posted by {$adminName}",
                'url'  => url('/admin/m/notices'),
                'icon' => 'bell',
            ]);
        });

        \App\Models\Notice::updated(function (\App\Models\Notice $notice) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('notice', "Notice updated: '{$notice->title}'", [
                'body' => "Updated by {$adminName}",
                'url'  => url('/admin/m/notices'),
                'icon' => 'pencil',
            ]);
        });

        \App\Models\Achievement::created(function (\App\Models\Achievement $achievement) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('achievement', "New achievement added: '{$achievement->title}'", [
                'body' => "Added by {$adminName}",
                'url'  => url('/admin/m/achievements'),
                'icon' => 'star',
            ]);
        });

        \App\Models\Achievement::updated(function (\App\Models\Achievement $achievement) {
            $adminName = auth()->user()?->name ?? 'System';
            AdminNotification::record('achievement', "Achievement updated: '{$achievement->title}'", [
                'body' => "Updated by {$adminName}",
                'url'  => url('/admin/m/achievements'),
                'icon' => 'star',
            ]);
        });

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
