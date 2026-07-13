<?php

namespace App\Services;

use App\Models\Testimonial;
use App\Core\Settings\Settings;

class SpamFilterService
{
    /**
     * Check if the given text contains any blocked profanity keywords.
     */
    public function hasProfanity(string $text): bool
    {
        $blockedWordsSetting = Settings::get('testimonial_blocked_words', 'abuse,fake,spam,cheat,worst,bad,worstschool,fraud');
        
        // Clean the input text
        $cleanText = strtolower($text);
        
        // Split setting words by comma
        $blockedWords = array_map('trim', explode(',', $blockedWordsSetting));
        $blockedWords = array_filter($blockedWords);

        foreach ($blockedWords as $word) {
            if (empty($word)) {
                continue;
            }
            if (str_contains($cleanText, strtolower($word))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect duplicate testimonials from the same author/IP within 1 hour.
     */
    public function isDuplicate(string $name, string $ip, string $text): bool
    {
        return Testimonial::query()
            ->where(function ($q) use ($ip, $name) {
                $q->where('ip_address', $ip)
                  ->orWhere('name', $name);
            })
            ->where('testimonial', trim($text))
            ->where('created_at', '>=', now()->subHour())
            ->exists();
    }
}
