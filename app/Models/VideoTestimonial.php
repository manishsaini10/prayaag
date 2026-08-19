<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoTestimonial extends BaseModel
{
    protected $casts = [
        'consent_confirmed' => 'boolean',
        'is_featured'       => 'boolean',
        'sort_order'        => 'integer',
        'views_count'       => 'integer',
        'duration_seconds'  => 'integer',
        'reviewed_at'       => 'datetime',
        'consent_signed_at' => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function tags(): HasMany
    {
        return $this->hasMany(VideoTestimonialTag::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(VideoTestimonialView::class);
    }

    // ----------------------------------------------------------------
    // Query Scopes
    // ----------------------------------------------------------------

    /** Only publicly visible testimonials — BOTH approved AND consented. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved')
                     ->where('consent_confirmed', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Filter by a tag type + value pair.
     * When either is empty, no filter is applied.
     */
    public function scopeMatchingTags(Builder $query, ?string $tagType, ?string $tagValue): Builder
    {
        if (! $tagType || ! $tagValue) {
            return $query;
        }

        return $query->whereHas('tags', function (Builder $q) use ($tagType, $tagValue) {
            $q->where('tag_type', $tagType)
              ->where('tag_value', $tagValue);
        });
    }

    // ----------------------------------------------------------------
    // Helpers & Accessors
    // ----------------------------------------------------------------

    /** Dynamic resolution of thumbnail URL with self-healing fallback if column is null/empty. */
    public function getResolvedThumbnailUrlAttribute(): string
    {
        if (! empty($this->thumbnail_url)) {
            return $this->thumbnail_url;
        }

        if (! empty($this->video_external_id)) {
            try {
                return app(\App\Core\Video\VideoManager::class)
                    ->driver($this->video_provider ?: 'youtube_unlisted')
                    ->getThumbnailUrl($this->video_external_id);
            } catch (\Throwable $e) {
                return "https://img.youtube.com/vi/{$this->video_external_id}/hqdefault.jpg";
            }
        }

        return '';
    }

    /** Returns true only when safe to display publicly. */
    public function isPubliclyVisible(): bool
    {
        return $this->status === 'approved' && $this->consent_confirmed === true;
    }

    /** Sync tags array: [['tag_type'=>'program','tag_value'=>'2026-27'], ...] */
    public function syncTags(array $tags): void
    {
        $this->tags()->delete();

        foreach ($tags as $tag) {
            if (! empty($tag['tag_type']) && ! empty($tag['tag_value'])) {
                $this->tags()->create([
                    'tag_type'  => $tag['tag_type'],
                    'tag_value' => $tag['tag_value'],
                ]);
            }
        }
    }
}
