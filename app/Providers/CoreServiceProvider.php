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
use App\Core\Builder\Widgets\AdmissionsPageWidget;
use App\Core\Builder\Widgets\DynamicWidget;
use App\Core\Builder\Widgets\MessMenuWidget;
use App\Core\Builder\Widgets\VideoTestimonialWidget;
use App\Core\Video\VideoManager;
use App\Models\WidgetDefinition;
use App\Core\Media\MediaManager;
use App\Core\Menu\MenuManager;
use App\Core\Resources\ResourceRegistry;
use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use App\Core\Builder\Widgets\PricingTableWidget;
use App\Core\Builder\Widgets\FaqAccordionWidget;
use App\Core\Builder\Widgets\CountdownTimerWidget;
use App\Core\Builder\Widgets\TeamMemberWidget;
use App\Core\Builder\Widgets\FlipBoxWidget;
use App\Core\Builder\Widgets\TimelineWidget;
use App\Core\Builder\Widgets\ProgressBarWidget;
use App\Core\Builder\Widgets\ImageComparisonWidget;
use App\Core\Builder\Widgets\TabsWidget;
use App\Core\Builder\Widgets\DualButtonWidget;
use App\Core\Builder\Widgets\AudioPlayerWidget;
use App\Core\Builder\Widgets\ChartWidget;
use App\Core\Builder\Widgets\CircleMenuWidget;
use App\Core\Builder\Widgets\DataTableWidget;
use App\Core\Builder\Widgets\EventCalendarWidget;
use App\Core\Builder\Widgets\WhatsAppChatWidget;
use App\Core\Builder\Widgets\ZoomMeetingWidget;
use App\Core\Builder\Widgets\AdvancedSliderWidget;
use App\Core\Builder\Widgets\AdvancedToggleWidget;
use App\Core\Builder\Widgets\ProtectedContentWidget;
use App\Core\Builder\Widgets\CreativeButtonWidget;
use App\Core\Builder\Widgets\FancyTextWidget;
use App\Core\Builder\Widgets\ImageHotspotWidget;
use App\Core\Builder\Widgets\ImageMorphingWidget;
use App\Core\Builder\Widgets\MotionTextWidget;
use App\Core\Builder\Widgets\StackedCardsWidget;
use App\Core\Builder\Widgets\UnfoldContentWidget;
use App\Core\Builder\Widgets\GlassMorphismWidget;
use App\Core\Builder\Widgets\ContentTickerWidget;
use App\Core\Builder\Widgets\CouponCodeWidget;
use App\Core\Builder\Widgets\PriceMenuWidget;
use App\Core\Builder\Widgets\ReviewsRatingsWidget;
use App\Core\Builder\Widgets\BackToTopWidget;
use App\Core\Builder\Widgets\BusinessHoursWidget;
use App\Core\Builder\Widgets\IconBoxWidget;
use App\Core\Builder\Widgets\ImageAccordionWidget;
use App\Core\Builder\Widgets\ImageBoxWidget;
use App\Core\Builder\Widgets\PageListWidget;
use App\Core\Builder\Widgets\SocialIconsWidget;
use App\Core\Builder\Widgets\DropCapsWidget;
use App\Core\Builder\Widgets\FunFactWidget;
use App\Core\Builder\Widgets\LottieAnimationWidget;
use App\Core\Builder\Widgets\MegaMenuWidget;
use App\Core\Builder\Widgets\HeaderInfoWidget;
use App\Core\Builder\Widgets\HeaderOffcanvasWidget;
use App\Core\Builder\Widgets\ClientLogoWidget;
use App\Core\Builder\Widgets\SocialShareWidget;
use App\Core\Builder\Widgets\CategoryListWidget;
use App\Core\Builder\Widgets\PostListWidget;
use App\Core\Builder\Widgets\AlumniPageWidget;
use App\Core\Builder\Widgets\BookListWidget;
use App\Core\Builder\Widgets\DownloadsPageWidget;
use App\Core\Builder\Widgets\ContactUsPageWidget;

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
        AdmissionsPageWidget::class,
        AlumniPageWidget::class,
        BookListWidget::class,
        DownloadsPageWidget::class,
        ContactUsPageWidget::class,
        // Media & Social Proof
        VideoTestimonialWidget::class,
        // PRO Suite
        PricingTableWidget::class,
        FaqAccordionWidget::class,
        CountdownTimerWidget::class,
        TeamMemberWidget::class,
        FlipBoxWidget::class,
        TimelineWidget::class,
        ProgressBarWidget::class,
        ImageComparisonWidget::class,
        TabsWidget::class,
        DualButtonWidget::class,
        AudioPlayerWidget::class,
        ChartWidget::class,
        CircleMenuWidget::class,
        DataTableWidget::class,
        EventCalendarWidget::class,
        WhatsAppChatWidget::class,
        ZoomMeetingWidget::class,
        AdvancedSliderWidget::class,
        AdvancedToggleWidget::class,
        ProtectedContentWidget::class,
        CreativeButtonWidget::class,
        FancyTextWidget::class,
        ImageHotspotWidget::class,
        ImageMorphingWidget::class,
        MotionTextWidget::class,
        StackedCardsWidget::class,
        UnfoldContentWidget::class,
        GlassMorphismWidget::class,
        ContentTickerWidget::class,
        CouponCodeWidget::class,
        PriceMenuWidget::class,
        ReviewsRatingsWidget::class,
        BackToTopWidget::class,
        BusinessHoursWidget::class,
        IconBoxWidget::class,
        ImageAccordionWidget::class,
        ImageBoxWidget::class,
        PageListWidget::class,
        SocialIconsWidget::class,
        DropCapsWidget::class,
        FunFactWidget::class,
        LottieAnimationWidget::class,
        MegaMenuWidget::class,
        HeaderInfoWidget::class,
        HeaderOffcanvasWidget::class,
        ClientLogoWidget::class,
        SocialShareWidget::class,
        CategoryListWidget::class,
        PostListWidget::class,
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
        $this->app->singleton(VideoManager::class);
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
