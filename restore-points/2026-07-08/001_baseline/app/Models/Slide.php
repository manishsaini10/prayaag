<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Child of Slider, reached through its parent.
 */
class Slide extends Model
{
    use HasUlids;

    protected $fillable = ['slider_id', 'image', 'heading', 'subheading', 'link_url', 'link_label', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }
}
