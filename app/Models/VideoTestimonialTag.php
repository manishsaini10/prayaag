<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoTestimonialTag extends Model
{
    protected $guarded = ['id'];

    public function testimonial(): BelongsTo
    {
        return $this->belongsTo(VideoTestimonial::class, 'video_testimonial_id');
    }
}
