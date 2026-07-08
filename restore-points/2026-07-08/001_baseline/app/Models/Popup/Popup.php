<?php

namespace App\Models\Popup;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Popup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'popups';

    protected $fillable = [
        'title', 'slug', 'type', 'status', 'category_id', 'template_id',
        'structure', 'settings', 'design', 'styles', 'custom_css', 'custom_js',
        'starts_at', 'ends_at',
        'use_recurring_schedule', 'recurring_schedule',
        'frequency_type', 'frequency_delay', 'frequency_x_days', 'max_views_per_user',
        'is_ab_test', 'ab_test_id',
        'view_count', 'impression_count', 'click_count', 'conversion_count',
        'priority', 'sort_order',
        'meta', 'noindex', 'is_template',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'structure' => 'array',
            'settings' => 'array',
            'design' => 'array',
            'styles' => 'array',
            'recurring_schedule' => 'array',
            'meta' => 'array',
            'use_recurring_schedule' => 'boolean',
            'is_ab_test' => 'boolean',
            'noindex' => 'boolean',
            'is_template' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'view_count' => 'integer',
            'impression_count' => 'integer',
            'click_count' => 'integer',
            'conversion_count' => 'integer',
            'priority' => 'integer',
            'sort_order' => 'integer',
            'frequency_delay' => 'integer',
            'frequency_x_days' => 'integer',
            'max_views_per_user' => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(PopupCategory::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PopupTemplate::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PopupRule::class);
    }

    public function triggers(): HasMany
    {
        return $this->rules()->where('type', 'trigger');
    }

    public function displayRules(): HasMany
    {
        return $this->rules()->where('type', 'display');
    }

    public function targetingRules(): HasMany
    {
        return $this->rules()->where('type', 'targeting');
    }

    public function frequencyRules(): HasMany
    {
        return $this->rules()->where('type', 'frequency');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PopupSchedule::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(PopupAnalytics::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(PopupLead::class);
    }

    public function abTest(): BelongsTo
    {
        return $this->belongsTo(PopupAbTest::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(PopupAsset::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PopupRevision::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(PopupActivityLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('status', ['active', 'scheduled'])
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('sort_order', 'asc');
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVisible(): bool
    {
        if (! in_array($this->status, ['active', 'scheduled'])) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        return true;
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->view_count === 0) return 0;
        return round(($this->conversion_count / $this->view_count) * 100, 2);
    }

    public function getCtrAttribute(): float
    {
        if ($this->impression_count === 0) return 0;
        return round(($this->click_count / $this->impression_count) * 100, 2);
    }
}
