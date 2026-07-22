<?php
namespace App\Core\Chatbot\Sentiment;

/**
 * Simple rule‑based sentiment analyser.
 * Returns one of: 'positive', 'negative', 'neutral'.
 */
class SentimentService
{
    protected array $positive = ['good', 'great', 'awesome', 'fantastic', 'excellent', 'happy'];
    protected array $negative = ['bad', 'terrible', 'sad', 'poor', 'hate', 'worst'];

    public function analyse(string $text): string
    {
        $text = strtolower($text);
        $pos = 0; $neg = 0;
        foreach ($this->positive as $word) {
            if (str_contains($text, $word)) $pos++;
        }
        foreach ($this->negative as $word) {
            if (str_contains($text, $word)) $neg++;
        }
        if ($pos > $neg) return 'positive';
        if ($neg > $pos) return 'negative';
        return 'neutral';
    }
}
?>
