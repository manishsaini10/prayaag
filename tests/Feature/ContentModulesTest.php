<?php

namespace Tests\Feature;

use App\Core\Builder\WidgetRegistry;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_excludes_drafts_and_future_posts(): void
    {
        Post::create(['title' => 'Live', 'slug' => 'live', 'status' => 'published', 'published_at' => now()->subDay()]);
        Post::create(['title' => 'Draft', 'slug' => 'draft', 'status' => 'draft']);
        Post::create(['title' => 'Future', 'slug' => 'future', 'status' => 'published', 'published_at' => now()->addDay()]);

        $this->assertSame(1, Post::published()->count());
    }

    public function test_post_tag_relationship_works(): void
    {
        $post = Post::create(['title' => 'Tagged', 'slug' => 'tagged', 'status' => 'published', 'published_at' => now()]);
        $tag = Tag::create(['name' => 'News', 'slug' => 'news']);
        $post->tags()->attach($tag->id);

        $this->assertTrue($post->fresh()->tags->contains('id', $tag->id));
    }

    public function test_latest_posts_widget_binds_to_real_data(): void
    {
        Post::create(['title' => 'Hello Post', 'slug' => 'hello-post', 'status' => 'published', 'published_at' => now()]);

        $html = app(WidgetRegistry::class)->render('latest_posts', ['limit' => 5]);

        $this->assertStringContainsString('Hello Post', $html);
        $this->assertStringContainsString('/hello-post', $html);
    }
}
