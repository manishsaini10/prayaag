<?php

namespace App\Models\Popup;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupAbTest extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'popup_ab_tests';

    protected $fillable = [
        'name', 'status', 'goal_type', 'winner_determination',
        'min_confidence', 'min_sample_size', 'traffic_split',
        'auto_winner', 'started_at', 'ended_at',
        'winner_id', 'results',
    ];

    protected function casts(): array
    {
        return [
            'min_confidence' => 'decimal:2',
            'min_sample_size' => 'integer',
            'traffic_split' => 'integer',
            'auto_winner' => 'boolean',
            'results' => 'array',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(PopupAbTestVariant::class, 'ab_test_id');
    }

    public function originals(): HasMany
    {
        return $this->variants()->where('variant_type', 'original');
    }

    public function variations(): HasMany
    {
        return $this->variants()->where('variant_type', 'variant');
    }

    public function popups(): HasMany
    {
        return $this->hasMany(Popup::class, 'ab_test_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(PopupAbTestVariant::class, 'winner_id');
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function canDetermineWinner(): bool
    {
        return $this->variants()->sum('view_count') >= $this->min_sample_size;
    }
}
