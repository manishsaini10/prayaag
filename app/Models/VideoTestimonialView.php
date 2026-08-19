<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoTestimonialView extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'watch_percentage' => 'integer',
        'cta_clicked'      => 'boolean',
        'viewed_at'        => 'datetime',
    ];

    public function testimonial(): BelongsTo
    {
        return $this->belongsTo(VideoTestimonial::class, 'video_testimonial_id');
    }
}
