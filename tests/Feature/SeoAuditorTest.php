<?php

namespace Tests\Feature;

use App\Core\Seo\SeoAuditor;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAuditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_returns_a_score_and_flags_missing_descriptions(): void
    {
        // A healthy page: H1 + body text → description auto-generates.
        $good = Page::create(['title' => 'Welcome', 'slug' => 'welcome', 'status' => 'published']);
        $c = $good->sections()->create(['section_type' => 'section', 'sort_order' => 0])
            ->rows()->create(['sort_order' => 0])
            ->columns()->create(['width' => 12, 'sort_order' => 0]);
        $c->widgets()->create(['widget_type' => 'heading', 'sort_order' => 0, 'settings' => ['text' => 'Welcome', 'level' => 1]]);
        $c->widgets()->create(['widget_type' => 'text', 'sort_order' => 1, 'settings' => ['content' => 'A descriptive paragraph about our academic programs and campus.']]);

        // A page with only an image → no text → empty description (critical).
        $empty = Page::create(['title' => 'Empty', 'slug' => 'empty', 'status' => 'published']);
        $empty->sections()->create(['section_type' => 'section', 'sort_order' => 0])
            ->rows()->create(['sort_order' => 0])
            ->columns()->create(['width' => 12, 'sort_order' => 0])
            ->widgets()->create(['widget_type' => 'image', 'sort_order' => 0, 'settings' => ['src' => 'https://example.com/x.jpg', 'alt' => '']]);

        $report = app(SeoAuditor::class)->run();

        $this->assertIsInt($report['score']);
        $this->assertGreaterThanOrEqual(0, $report['score']);
        $this->assertLessThanOrEqual(100, $report['score']);
        $this->assertNotEmpty($report['checks']);

        $descCheck = collect($report['checks'])->firstWhere('id', 'desc_present');
        $this->assertSame('crit', $descCheck['status']);
        $this->assertContains('/empty', collect($descCheck['items'])->pluck('path')->all());
    }
}
