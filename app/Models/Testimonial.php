<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Testimonial extends BaseModel
{
    protected $casts = [
        'is_published'     => 'boolean',
        'sort_order'       => 'integer',
        'featured'         => 'boolean',
        'rating'           => 'integer',
        'display_location' => 'array',
        'approved_at'      => 'datetime',
        'is_verified'      => 'boolean',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (self $t) {
            if ($t->sort_order === null || $t->sort_order === '') {
                $t->sort_order = (int) (static::query()->max('sort_order') ?? 0) + 1;
            }
            if (!$t->status) {
                // Check raw attributes to see if is_published was explicitly set to false
                $rawIsPublished = $t->attributes['is_published'] ?? null;
                $t->status = ($rawIsPublished === false || $rawIsPublished === 0 || $rawIsPublished === '0') ? 'pending' : 'approved';
            }
            $t->attributes['is_published'] = ($t->status === 'approved');
        });

        static::saving(function (self $t) {
            // Keep legacy attributes synchronized in DB
            $t->attributes['author'] = $t->name;
            $t->attributes['quote'] = $t->testimonial;
            $t->attributes['photo'] = $t->image;
            
            if ($t->status) {
                $t->attributes['is_published'] = ($t->status === 'approved');
            }
        });
    }

    // --- Legacy Column Mappings / Accessors ---

    public function getAuthorAttribute(): ?string
    {
        return $this->name;
    }

    public function setAuthorAttribute(?string $value): void
    {
        $this->name = $value;
        $this->attributes['author'] = $value;
    }

    public function getQuoteAttribute(): ?string
    {
        return $this->testimonial;
    }

    public function setQuoteAttribute(?string $value): void
    {
        $this->testimonial = $value;
        $this->attributes['quote'] = $value;
    }

    public function getPhotoAttribute(): ?string
    {
        return $this->image;
    }

    public function setPhotoAttribute(?string $value): void
    {
        $this->image = $value;
        $this->attributes['photo'] = $value;
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function setIsPublishedAttribute($value): void
    {
        $this->status = $value ? 'approved' : 'pending';
        $this->attributes['is_published'] = (bool) $value;
    }

    public function getRoleAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $parts = [];
        if (!empty($this->relation)) {
            $parts[] = $this->relation;
        }
        if (!empty($this->student_name)) {
            $parts[] = 'of ' . $this->student_name;
        }
        if (!empty($this->class)) {
            $parts[] = '(' . $this->class . ')';
        }

        return implode(' ', $parts);
    }

    // --- Query Scopes ---

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeForLocation(Builder $query, string $location): Builder
    {
        return $query->whereJsonContains('display_location', $location);
    }
}
