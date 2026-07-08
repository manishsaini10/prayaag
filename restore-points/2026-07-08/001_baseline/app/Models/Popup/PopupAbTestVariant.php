<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PopupAbTestVariant extends Model
{
    use SoftDeletes;

    protected $table = 'popup_ab_test_variants';

    protected $fillable = [
        'ab_test_id', 'name', 'variant_type',
        'structure', 'settings', 'design',
        'view_count', 'conversion_count',
    ];

    protected function casts(): array
    {
        return [
            'structure' => 'array',
            'settings' => 'array',
            'design' => 'array',
            'view_count' => 'integer',
            'conversion_count' => 'integer',
        ];
    }

    public function abTest(): BelongsTo
    {
        return $this->belongsTo(PopupAbTest::class, 'ab_test_id');
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->view_count === 0) return 0;
        return round(($this->conversion_count / $this->view_count) * 100, 2);
    }
}
