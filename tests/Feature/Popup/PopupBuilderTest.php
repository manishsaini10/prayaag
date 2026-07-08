<?php

namespace Tests\Feature\Popup;

use App\Core\Popup\Actions\CaptureLeadAction;
use App\Core\Popup\Actions\CreatePopupAction;
use App\Core\Popup\Actions\DuplicatePopupAction;
use App\Core\Popup\Actions\PublishPopupAction;
use App\Core\Popup\Actions\TrackAnalyticsAction;
use App\Core\Popup\DTOs\AnalyticsDTO;
use App\Core\Popup\DTOs\LeadDTO;
use App\Core\Popup\DTOs\PopupDTO;
use App\Core\Popup\Engines\AbTestEngine;
use App\Core\Popup\Engines\RenderingEngine;
use App\Core\Popup\Engines\TriggerEngine;
use App\Core\Popup\Repositories\PopupRepository;
use App\Core\Popup\Services\AnalyticsService;
use App\Core\Popup\Services\PopupService;
use App\Core\Popup\Services\RuleEngineService;
use App\Core\Popup\Services\TemplateService;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupAbTest;
use App\Models\Popup\PopupAbTestVariant;
use App\Models\Popup\PopupAnalytics;
use App\Models\Popup\PopupCategory;
use App\Models\Popup\PopupLead;
use App\Models\Popup\PopupRule;
use App\Models\Popup\PopupTemplate;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PopupBuilderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;
    private PopupRepository $repository;
    private PopupService $popupService;
    private RuleEngineService $ruleEngine;
    private TemplateService $templateService;
    private AnalyticsService $analyticsService;
    private RenderingEngine $renderingEngine;
    private AbTestEngine $abTestEngine;
    private TriggerEngine $triggerEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => '']);
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole($role);

        $this->repository = app(PopupRepository::class);
        $this->popupService = app(PopupService::class);
        $this->ruleEngine = app(RuleEngineService::class);
        $this->templateService = app(TemplateService::class);
        $this->analyticsService = app(AnalyticsService::class);
        $this->renderingEngine = app(RenderingEngine::class);
        $this->abTestEngine = app(AbTestEngine::class);
        $this->triggerEngine = app(TriggerEngine::class);
    }

    /** @test */
    public function it_can_create_a_popup()
    {
        $dto = PopupDTO::fromArray([
            'title' => 'Test Popup',
            'type' => 'modal',
            'status' => 'draft',
            'structure' => [
                'container' => ['type' => 'container', 'styles' => ['padding' => '20']],
                'rows' => [
                    ['columns' => [['width' => 12, 'widgets' => [
                        ['type' => 'heading', 'content' => 'Hello World', 'settings' => ['tag' => 'h2']],
                    ]]]]
                ]
            ],
            'settings' => ['width' => 600, 'animation' => 'fade'],
        ]);

        $popup = $this->popupService->create($dto);

        $this->assertNotNull($popup);
        $this->assertEquals('Test Popup', $popup->title);
        $this->assertEquals('modal', $popup->type);
        $this->assertEquals('draft', $popup->status);
        $this->assertDatabaseHas('popups', ['id' => $popup->id]);
    }

    /** @test */
    public function it_can_publish_a_popup()
    {
        $popup = Popup::factory()->draft()->create();
        $result = $this->popupService->publish($popup);

        $this->assertEquals('active', $result->status);
        $this->assertDatabaseHas('popups', ['id' => $popup->id, 'status' => 'active']);
    }

    /** @test */
    public function it_can_duplicate_a_popup()
    {
        $popup = Popup::factory()->active()->create();
        $popup->rules()->create([
            'type' => 'trigger', 'rule_key' => 'page_load', 'condition' => 'is', 'value' => 'true',
        ]);

        $clone = $this->repository->duplicate($popup);

        $this->assertNotEquals($popup->id, $clone->id);
        $this->assertEquals('draft', $clone->status);
        $this->assertStringContainsString('(Copy)', $clone->title);
        $this->assertEquals(1, $clone->rules()->count());
    }

    /** @test */
    public function it_can_soft_delete_a_popup()
    {
        $popup = Popup::factory()->create();
        $this->popupService->delete($popup);

        $this->assertSoftDeleted($popup);
    }

    /** @test */
    public function it_can_restore_a_popup()
    {
        $popup = Popup::factory()->create();
        $popup->delete();

        $restored = $this->repository->restore($popup->id);

        $this->assertNotNull($restored);
        $this->assertFalse($restored->trashed());
    }

    /** @test */
    public function it_creates_revision_on_save()
    {
        $dto = PopupDTO::fromArray([
            'title' => 'Revision Test',
            'type' => 'modal',
            'status' => 'draft',
            'structure' => ['container' => ['type' => 'container'], 'rows' => []],
        ]);

        $popup = $this->popupService->create($dto);

        $this->assertEquals(1, $popup->revisions()->count());

        // Update and create another revision
        $dto2 = PopupDTO::fromArray([
            'title' => 'Revision Test Updated',
            'type' => 'modal',
            'status' => 'active',
            'structure' => ['container' => ['type' => 'container'], 'rows' => []],
        ]);
        $this->popupService->update($popup, $dto2);

        $this->assertEquals(2, $popup->fresh()->revisions()->count());
    }

    /** @test */
    public function it_can_restore_a_revision()
    {
        $popup = Popup::factory()->create(['structure' => ['version' => 1]]);
        $popup->revisions()->create([
            'version' => 1,
            'structure' => ['version' => 1],
            'note' => 'Initial',
            'created_by' => $this->admin->id,
        ]);
        $popup->update(['structure' => ['version' => 2]]);
        $popup->revisions()->create([
            'version' => 2,
            'structure' => ['version' => 2],
            'note' => 'Update',
            'created_by' => $this->admin->id,
        ]);

        $restored = $this->popupService->restoreRevision($popup, 1);

        $this->assertEquals(['version' => 1], $restored->structure);
    }

    /** @test */
    public function it_can_add_rules_to_popup()
    {
        $popup = Popup::factory()->create();

        $this->popupService->updateRules($popup, 'trigger', [
            ['rule_key' => 'page_load', 'condition' => 'is', 'value' => 'true'],
            ['rule_key' => 'exit_intent', 'condition' => 'is', 'value' => 'true'],
        ]);

        $this->assertEquals(2, $popup->fresh()->triggers()->count());
    }

    /** @test */
    public function it_tracks_analytics()
    {
        $popup = Popup::factory()->create();

        $dto = new AnalyticsDTO(
            popupId: $popup->id,
            eventType: 'view',
            url: 'http://test.com',
            sessionId: 'test-session',
        );

        app(TrackAnalyticsAction::class)->execute($dto);

        $this->assertDatabaseHas('popup_analytics', [
            'popup_id' => $popup->id,
            'event_type' => 'view',
        ]);

        $this->assertEquals(1, $popup->fresh()->view_count);
    }

    /** @test */
    public function it_captures_leads()
    {
        $popup = Popup::factory()->create();

        $dto = new LeadDTO(
            popupId: $popup->id,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '+1234567890',
            formData: ['name' => 'John Doe', 'email' => 'john@example.com'],
            source: 'http://test.com/popup',
        );

        app(CaptureLeadAction::class)->execute($dto);

        $this->assertDatabaseHas('popup_leads', [
            'popup_id' => $popup->id,
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }

    /** @test */
    public function it_evaluates_display_rules()
    {
        $popup = Popup::factory()->active()->create();

        // Add display rule: show only on homepage
        $popup->rules()->create([
            'type' => 'display',
            'rule_key' => 'homepage',
            'condition' => 'is',
            'value' => 'true',
        ]);

        $context = ['path' => '/home', 'url' => 'http://test.com/home'];
        $this->assertTrue($this->ruleEngine->evaluateDisplayRules($popup, $context));

        $context2 = ['path' => '/about', 'url' => 'http://test.com/about'];
        $this->assertFalse($this->ruleEngine->evaluateDisplayRules($popup, $context2));
    }

    /** @test */
    public function it_evaluates_frequency_rules()
    {
        $popup = Popup::factory()->active()->create(['frequency_type' => 'once_per_session']);

        $this->assertTrue($this->ruleEngine->evaluateFrequencyRules($popup, []));

        $this->ruleEngine->markShown($popup);

        $this->assertFalse($this->ruleEngine->evaluateFrequencyRules($popup, []));
    }

    /** @test */
    public function it_returns_triggers_config()
    {
        $popup = Popup::factory()->active()->create();
        $popup->rules()->create([
            'type' => 'trigger',
            'rule_key' => 'time_delay',
            'condition' => 'is',
            'value' => '5000',
            'extra' => ['unit' => 'ms'],
        ]);

        $triggers = $this->triggerEngine->getTriggerConfig($popup);

        $this->assertCount(1, $triggers);
        $this->assertEquals('time_delay', $triggers->first()['key']);
        $this->assertEquals('5000', $triggers->first()['value']);
    }

    /** @test */
    public function it_renders_popup_html()
    {
        $popup = Popup::factory()->active()->create([
            'structure' => [
                'container' => ['type' => 'container', 'styles' => ['padding' => '20']],
                'rows' => [
                    ['columns' => [['width' => 12, 'widgets' => [
                        ['type' => 'heading', 'content' => 'Test Heading', 'settings' => ['tag' => 'h2']],
                        ['type' => 'paragraph', 'content' => 'Test paragraph content.', 'settings' => []],
                    ]]]]
                ]
            ],
            'settings' => ['animation' => 'fade', 'width' => 600, 'overlay' => true],
        ]);

        $html = $this->renderingEngine->render($popup);

        $this->assertStringContainsString('Test Heading', $html);
        $this->assertStringContainsString('Test paragraph content.', $html);
        $this->assertStringContainsString('popup', $html);
    }

    /** @test */
    public function it_can_create_ab_test()
    {
        $test = PopupAbTest::create([
            'name' => 'Button Color Test',
            'goal_type' => 'click',
            'traffic_split' => 50,
            'min_sample_size' => 100,
        ]);

        $original = $test->variants()->create([
            'name' => 'Original',
            'variant_type' => 'original',
            'structure' => ['version' => 'a'],
        ]);

        $variant = $test->variants()->create([
            'name' => 'Variant B',
            'variant_type' => 'variant',
            'structure' => ['version' => 'b'],
        ]);

        $this->assertCount(2, $test->fresh()->variants);
    }

    /** @test */
    public function it_determines_ab_test_winner()
    {
        $test = PopupAbTest::create([
            'name' => 'Test',
            'status' => 'running',
            'goal_type' => 'click',
            'min_sample_size' => 10,
            'auto_winner' => false,
        ]);

        $original = $test->variants()->create([
            'name' => 'Original',
            'variant_type' => 'original',
            'view_count' => 100,
            'conversion_count' => 5,
        ]);

        $variant = $test->variants()->create([
            'name' => 'Variant',
            'variant_type' => 'variant',
            'view_count' => 100,
            'conversion_count' => 20,
        ]);

        $winner = $this->abTestEngine->determineWinner($test);

        $this->assertNotNull($winner);
        $this->assertEquals($variant->id, $winner->id);
        $this->assertEquals('completed', $test->fresh()->status);
    }

    /** @test */
    public function it_templates_can_be_seeded()
    {
        $this->templateService->seedDefaults();

        $templates = PopupTemplate::where('is_built_in', true)->get();
        $this->assertGreaterThan(0, $templates->count());
    }

    /** @test */
    public function it_gets_popup_stats()
    {
        $popup = Popup::factory()->create();

        // Create some analytics data
        PopupAnalytics::create([
            'popup_id' => $popup->id,
            'event_type' => 'view',
            'occurred_at' => now()->subHours(2),
        ]);
        PopupAnalytics::create([
            'popup_id' => $popup->id,
            'event_type' => 'view',
            'occurred_at' => now()->subHour(),
        ]);
        PopupAnalytics::create([
            'popup_id' => $popup->id,
            'event_type' => 'click',
            'occurred_at' => now(),
        ]);

        $stats = $this->analyticsService->getPopupStats($popup->id, '7d');

        $this->assertArrayHasKey('views', $stats);
        $this->assertEquals(2, $stats['views']);
        $this->assertEquals(1, $stats['clicks']);
    }

    /** @test */
    public function it_gets_dashboard_stats()
    {
        Popup::factory(3)->active()->create();
        Popup::factory(2)->draft()->create();

        $stats = $this->analyticsService->getDashboardStats();

        $this->assertArrayHasKey('total_popups', $stats);
        $this->assertEquals(5, $stats['total_popups']);
        $this->assertEquals(3, $stats['active_popups']);
    }

    /** @test */
    public function it_creates_popup_from_template()
    {
        $this->templateService->seedDefaults();
        $template = PopupTemplate::where('is_built_in', true)->first();

        $dto = PopupDTO::fromArray([
            'title' => 'From Template',
            'type' => $template->type,
            'status' => 'draft',
        ]);

        $popup = $this->popupService->create($dto, $template->id);

        $this->assertEquals($template->structure, $popup->structure);
        $this->assertEquals($template->settings, $popup->settings);
    }

    /** @test */
    public function it_logs_activity()
    {
        $popup = Popup::factory()->create();

        $this->popupService->publish($popup);

        $this->assertDatabaseHas('popup_activity_logs', [
            'popup_id' => $popup->id,
            'action' => 'status_changed',
        ]);
    }

    /** @test */
    public function it_scopes_visible_popups()
    {
        $active = Popup::factory()->active()->create();
        $scheduled = Popup::factory()->scheduled()->create();
        $expired = Popup::factory()->expired()->create();
        $draft = Popup::factory()->draft()->create();

        $visible = Popup::visible()->get();

        $this->assertTrue($visible->contains('id', $active->id));
        $this->assertTrue($visible->contains('id', $scheduled->id));
        $this->assertFalse($visible->contains('id', $expired->id));
        $this->assertFalse($visible->contains('id', $draft->id));
    }

    /** @test */
    public function it_calculates_conversion_rate()
    {
        $popup = Popup::factory()->create([
            'view_count' => 100,
            'conversion_count' => 25,
        ]);

        $this->assertEquals(25.0, $popup->conversion_rate);
    }

    /** @test */
    public function it_calculates_ctr()
    {
        $popup = Popup::factory()->create([
            'impression_count' => 200,
            'click_count' => 30,
        ]);

        $this->assertEquals(15.0, $popup->ctr);
    }
}
