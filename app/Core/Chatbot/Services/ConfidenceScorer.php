<?php

namespace App\Core\Chatbot\Services;

class ConfidenceScorer
{
    private const LOW_CONFIDENCE_PHRASES = [
        'i am not sure', "i don't know", "i'm not sure", "i cannot", "i can't",
        'not available', 'no information', 'unable to', 'please contact',
        'i do not have', "i don't have", 'not specified', 'not mentioned',
        'i am unable', "i'm unable", 'may want to contact', 'please check',
        'i would recommend', 'you might want to', 'as per my knowledge',
    ];

    private const HIGH_CONFIDENCE_PATTERNS = [
        'prayaag school offers', 'our school has', 'the admission process',
        'fee structure', 'you can visit', 'please contact our',
        'the school is located', 'our facilities include', 'curriculum includes',
        'academic year begins', 'you can apply', 'required documents',
    ];

    public function score(string $response, string $intent): array
    {
        $lower = strtolower($response);
        $length = strlen($response);
        $lowHits = 0;
        $highHits = 0;

        foreach (self::LOW_CONFIDENCE_PHRASES as $phrase) {
            if (str_contains($lower, $phrase)) {
                $lowHits++;
            }
        }

        foreach (self::HIGH_CONFIDENCE_PATTERNS as $pattern) {
            if (str_contains($lower, $pattern)) {
                $highHits++;
            }
        }

        $baseScore = 0.7;
        $baseScore -= ($lowHits * 0.15);
        $baseScore += ($highHits * 0.1);
        $baseScore = min(1.0, max(0.0, $baseScore));

        $level = 'high';
        if ($baseScore < 0.4) {
            $level = 'low';
        } elseif ($baseScore < 0.65) {
            $level = 'medium';
        }

        $needsHuman = $baseScore < 0.45
            || $lowHits >= 2
            || ($intent === 'complaint' && $baseScore < 0.7);

        return [
            'score' => round($baseScore, 2),
            'level' => $level,
            'needs_human' => $needsHuman,
            'low_confidence_hits' => $lowHits,
            'high_confidence_hits' => $highHits,
        ];
    }
}
