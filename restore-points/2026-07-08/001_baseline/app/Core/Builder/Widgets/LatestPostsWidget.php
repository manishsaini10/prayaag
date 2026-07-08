<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Builder\Concerns\RendersCollection;
use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Dynamic widget: latest published posts, in 8 premium layouts.
 */
class LatestPostsWidget extends AbstractWidget
{
    use RendersCollection;

    public function type(): string
    {
        return 'latest_posts';
    }

    public function label(): string
    {
        return 'Latest Posts';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return $this->collectionDefaults('grid', 6);
    }

    public function fieldOptions(): array
    {
        return ['layout' => $this->collectionLayouts()];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $limit = max(1, (int) $this->setting($settings, 'limit', 6));
        $posts = Post::published()->latest('published_at')->limit($limit)->get();

        $icon = $this->collectionIcon('news');
        $items = [];
        foreach ($posts as $post) {
            $image = (string) ($post->image ?? $post->cover ?? $post->featured_image ?? $post->thumbnail ?? '');
            $excerpt = (string) ($post->excerpt ?? $post->summary ?? '');
            if ($excerpt === '' && ! empty($post->body)) {
                $excerpt = Str::limit(trim(strip_tags((string) $post->body)), 140);
            }

            $items[] = [
                'image'   => $image,
                'icon'    => $image === '' ? $icon : '',
                'eyebrow' => (string) ($post->category ?? ''),
                'title'   => (string) $post->title,
                'text'    => $excerpt,
                'meta'    => optional($post->published_at)->format('M j, Y') ?: '',
                'href'    => '/' . $this->e($post->slug),
            ];
        }

        return $this->renderCollection($items, $settings, ['accent' => 'var(--navy-3)', 'default' => 'grid']);
    }
}
