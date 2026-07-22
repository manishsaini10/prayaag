<?php

namespace App\Providers;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\Widgets\AcademicProgramsWidget;
use App\Core\Builder\Widgets\AchievementsWidget;
use App\Core\Builder\Widgets\AdmissionProcessWidget;
use App\Core\Builder\Widgets\AnnouncementBarWidget;
use App\Core\Builder\Widgets\FacilitiesWidget;
use App\Core\Builder\Widgets\FloatingActionWidget;
use App\Core\Builder\Widgets\MapWidget;
use App\Core\Builder\Widgets\AdmissionCtaWidget;
use App\Core\Builder\Widgets\BreadcrumbWidget;
use App\Core\Builder\Widgets\ButtonWidget;
use App\Core\Builder\Widgets\CampusWidget;
use App\Core\Builder\Widgets\ContactFormWidget;
use App\Core\Builder\Widgets\DownloadsWidget;
use App\Core\Builder\Widgets\GalleryWidget;
use App\Core\Builder\Widgets\GlimpsesWidget;
use App\Core\Builder\Widgets\HeadingWidget;
use App\Core\Builder\Widgets\HeroWidget;
use App\Core\Builder\Widgets\HtmlWidget;
use App\Core\Builder\Widgets\ImageWidget;
use App\Core\Builder\Widgets\InstagramWidget;
use App\Core\Builder\Widgets\JobListingsWidget;
use App\Core\Builder\Widgets\LatestPostsWidget;
use App\Core\Builder\Widgets\LeadershipWidget;
use App\Core\Builder\Widgets\LifeWidget;
use App\Core\Builder\Widgets\NewsletterWidget;
use App\Core\Builder\Widgets\NewsWidget;
use App\Core\Builder\Widgets\NoticeBoardWidget;
use App\Core\Builder\Widgets\QuickLinksWidget;
use App\Core\Builder\Widgets\SliderWidget;
use App\Core\Builder\Widgets\StatsWidget;
use App\Core\Builder\Widgets\TestimonialsWidget;
use App\Core\Builder\Widgets\TestimonialsCardsWidget;
use App\Core\Builder\Widgets\TestimonialFormWidget;
use App\Core\Builder\Widgets\TestimonialPageWidget;
use App\Core\Builder\Widgets\TextWidget;
use App\Core\Builder\Widgets\UpcomingEventsWidget;
use App\Core\Builder\Widgets\VideosWidget;
use App\Core\Builder\WidgetRegistry;
use App\Core\Builder\Widgets\DynamicWidget;
use App\Core\Builder\Widgets\MessMenuWidget;
use App\Models\WidgetDefinition;
use App\Core\Media\MediaManager;
use App\Core\Menu\MenuManager;
use App\Core\Resources\ResourceRegistry;
use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /** Default page-builder widgets registered at boot. */
    protected array $defaultWidgets = [
        // Static
        HeadingWidget::class,
        TextWidget::class,
        ImageWidget::class,
        ButtonWidget::class,
        HtmlWidget::class,
        // Dynamic data-binding
        LatestPostsWidget::class,
        NoticeBoardWidget::class,
        UpcomingEventsWidget::class,
        JobListingsWidget::class,
        TestimonialsWidget::class,
        DownloadsWidget::class,
        // Media
        SliderWidget::class,
        GalleryWidget::class,
        // Forms
        ContactFormWidget::class,
        TestimonialFormWidget::class,
        TestimonialPageWidget::class,
        NewsletterWidget::class,
        // School home sections
        HeroWidget::class,
        QuickLinksWidget::class,
        LeadershipWidget::class,
        StatsWidget::class,
        TestimonialsCardsWidget::class,
        NewsWidget::class,
        CampusWidget::class,
        AchievementsWidget::class,
        LifeWidget::class,
        GlimpsesWidget::class,
        VideosWidget::class,
        AdmissionCtaWidget::class,
        // Navigation
        BreadcrumbWidget::class,
        // School sections (additional / reusable)
        AnnouncementBarWidget::class,
        AdmissionProcessWidget::class,
        FacilitiesWidget::class,
        AcademicProgramsWidget::class,
        MapWidget::class,
        FloatingActionWidget::class,
        InstagramWidget::class,
        MessMenuWidget::class,
    ];

    public function register(): void
    {
        $this->app->singleton(ResourceRegistry::class);
        $this->app->singleton(SettingsManager::class);
        $this->app->singleton(MediaManager::class);
        $this->app->singleton(WidgetRegistry::class);
        $this->app->singleton(PageRenderer::class);
        $this->app->singleton(MenuManager::class);
        $this->app->singleton(ThemeRenderer::class);
    }

    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
                return true;
            }

            return null;
        });

        // Register the default widgets. New/plugin widgets register the same way.
        $registry = $this->app->make(WidgetRegistry::class);
        foreach ($this->defaultWidgets as $widget) {
            $registry->register(new $widget());
        }

        // Admin-defined custom widgets (Widget Builder). Registered after the
        // built-ins so they appear in the page-builder palette automatically.
        try {
            if (Schema::hasTable('widget_definitions')) {
                foreach (WidgetDefinition::where('is_active', true)->get() as $def) {
                    $registry->register(new DynamicWidget(
                        (string) $def->slug,
                        (string) $def->name,
                        (string) ($def->category ?: 'custom'),
                        (array) ($def->fields ?? []),
                        (string) ($def->template ?? ''),
                    ));
                }
            }
        } catch (\Throwable $e) {
            // Table not migrated yet or DB unavailable — skip silently.
        }
    }
}
