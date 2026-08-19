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
        $this->app->singleton(\App\Core\Mail\MailManager::class);
    }

    public function boot(): void
    {
        // --- Register custom 'google' storage driver ---
        \Illuminate\Support\Facades\Storage::extend('google', function ($app, $config) {
            $client = new \Google\Client();
            $client->setClientId($config['clientId'] ?? '');
            $client->setClientSecret($config['clientSecret'] ?? '');
            $client->refreshToken($config['refreshToken'] ?? '');

            $service = new \Google\Service\Drive($client);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/');
            $driver = new \League\Flysystem\Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });

        // --- Register public-forms Rate Limiter ---
        \Illuminate\Support\Facades\RateLimiter::for('public-forms', function (\Illuminate\Http\Request $request) {
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perDay(20)->by($request->ip()),
            ];
        });

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

        // --- Cache Invalidation Triggers ---
        $forgetChrome = function () {
            Cache::forget('theme.header');
            \App\Core\Menu\MenuManager::flush();
        };
        Menu::saved($forgetChrome);
        Menu::deleted($forgetChrome);
        MenuItem::saved($forgetChrome);
        MenuItem::deleted($forgetChrome);

        // --- Mess Menu Cache Invalidation ---
        $clearMessMenuCache = function () {
            \App\Core\Mess\Services\MessMenuService::flush();
            try {
                $pages = \App\Models\Page::all();
                $renderer = app(\App\Core\Builder\PageRenderer::class);
                foreach ($pages as $page) {
                    $renderer->forget($page);
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        };
        \App\Models\Mess\MessMenu::saved($clearMessMenuCache);
        \App\Models\Mess\MessMenu::deleted($clearMessMenuCache);
        \App\Models\Mess\MessMenuItem::saved($clearMessMenuCache);
        \App\Models\Mess\MessMenuItem::deleted($clearMessMenuCache);
        \App\Models\Mess\MessMenuSpecialDay::saved($clearMessMenuCache);
        \App\Models\Mess\MessMenuSpecialDay::deleted($clearMessMenuCache);

        // --- Testimonials & Job Listings Cache Invalidation ---
        $clearTestimonials = fn () => Cache::forget('testimonials.featured');
        \App\Models\Testimonial::saved($clearTestimonials);
        \App\Models\Testimonial::deleted($clearTestimonials);

        $clearJobListings = fn () => Cache::forget('job_listings.open');
        \App\Models\JobListing::saved($clearJobListings);
        \App\Models\JobListing::deleted($clearJobListings);

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

        // --- Video Testimonials: bust page cache + admin notifications ---
        $bustVideoCache = function () {
            rescue(function () {
                $renderer = app(\App\Core\Builder\PageRenderer::class);
                foreach (\App\Models\Page::all() as $page) {
                    $renderer->forget($page);
                }
            }, null, false);
        };

        \App\Models\VideoTestimonial::created(function (\App\Models\VideoTestimonial $vt) {
            AdminNotification::record('video_testimonial', "New video testimonial submitted: '{$vt->title}'", [
                'body' => 'Awaiting moderation review',
                'url'  => url('/admin/video-testimonials?status=pending'),
                'icon' => 'video-camera',
            ]);
        });

        \App\Models\VideoTestimonial::saved(function (\App\Models\VideoTestimonial $vt) use ($bustVideoCache) {
            // Only bust cache when status or consent changes — avoid bust on every field edit
            if ($vt->wasChanged('status') || $vt->wasChanged('consent_confirmed')) {
                $bustVideoCache();
            }
        });

        \App\Models\VideoTestimonial::deleted($bustVideoCache);
    }
}
