<?php

namespace App\Events;

use App\Models\VideoTestimonial;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoTestimonialRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly VideoTestimonial $testimonial,
        public readonly ?string $reason = null,
    ) {}
}
