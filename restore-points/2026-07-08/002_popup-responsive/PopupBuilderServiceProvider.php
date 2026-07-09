<?php

namespace App\Providers;

use App\Core\Popup\Actions\CaptureLeadAction;
use App\Core\Popup\Actions\CreatePopupAction;
use App\Core\Popup\Actions\DuplicatePopupAction;
use App\Core\Popup\Actions\PublishPopupAction;
use App\Core\Popup\Actions\TrackAnalyticsAction;
use App\Core\Popup\Engines\AbTestEngine;
use App\Core\Popup\Engines\RenderingEngine;
use App\Core\Popup\Engines\TriggerEngine;
use App\Core\Popup\Listeners\PopupEventSubscriber;
use App\Core\Popup\Repositories\PopupRepository;
use App\Core\Popup\Services\AnalyticsService;
use App\Core\Popup\Services\PopupService;
use App\Core\Popup\Services\RuleEngineService;
use App\Core\Popup\Services\TemplateService;
use App\Models\Popup\Popup;
use App\Policies\Popup\PopupPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PopupBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            config_path('popup-builder.php'), 'popup-builder'
        );

        // Bind services
        $this->app->singleton(PopupRepository::class);
        $this->app->singleton(PopupService::class);
        $this->app->singleton(RuleEngineService::class);
        $this->app->singleton(TemplateService::class);
        $this->app->singleton(AnalyticsService::class);
        $this->app->singleton(RenderingEngine::class);
        $this->app->singleton(AbTestEngine::class);
        $this->app->singleton(TriggerEngine::class);

        // Bind actions
        $this->app->singleton(CreatePopupAction::class);
        $this->app->singleton(PublishPopupAction::class);
        $this->app->singleton(DuplicatePopupAction::class);
        $this->app->singleton(CaptureLeadAction::class);
        $this->app->singleton(TrackAnalyticsAction::class);
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(database_path('migrations/Popup'));

        // Load routes
        $this->loadRoutesFrom(base_path('routes/popup/admin.php'));
        $this->loadRoutesFrom(base_path('routes/popup/api.php'));

        // Load views
        $this->loadViewsFrom(resource_path('views'), 'popup-builder');

        // Load translations
        $this->loadTranslationsFrom(lang_path('en/popup-builder'), 'popup-builder');

        // Register policies
        Gate::policy(Popup::class, PopupPolicy::class);

        // Register event subscribers
        Event::subscribe(PopupEventSubscriber::class);

        // Register Blade components
        Blade::component('popup-builder-assets', \App\View\Components\Popup\PopupBuilderAssets::class);
        Blade::component('popup-render', \App\View\Components\Popup\PopupRender::class);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\Popup\SeedPopupTemplates::class,
                \App\Console\Commands\Popup\CleanupAnalytics::class,
            ]);
        }

        // Publish assets
        $this->publishes([
            __DIR__ . '/../../config/popup-builder.php' => config_path('popup-builder.php'),
            __DIR__ . '/../../public/js/popup-builder' => public_path('js/popup-builder'),
            __DIR__ . '/../../public/css/popup-builder' => public_path('css/popup-builder'),
        ], 'popup-builder-assets');

        $this->publishes([
            __DIR__ . '/../../database/migrations/Popup' => database_path('migrations'),
        ], 'popup-builder-migrations');

    }
}
