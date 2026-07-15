<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\ChatbotSetting;
use App\Models\Chatbot\Enterprise\Embedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextEmbeddingService
{
    private const BATCH_SIZE = 10;
    private const DIMENSIONS = 768;

    public function embed(string $content, string $model = 'text-embedding-004'): array
    {
        $settings = ChatbotSetting::first();
        $aiConfig = ($settings?->settings_data ?? [])['ai'] ?? [];
        $apiKey = $aiConfig['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->fallbackEmbed($content);
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:embedContent?key={$apiKey}";
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, [
                'model' => "models/{$model}",
                'content' => ['parts' => [['text' => mb_substr($content, 0, 8000)]]],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['embedding']['values'] ?? $this->fallbackEmbed($content);
            }
        } catch (\Exception $e) {
            Log::error('Embedding API error: ' . $e->getMessage());
        }

        return $this->fallbackEmbed($content);
    }

    public function embedBatch(array $texts, string $model = 'text-embedding-004'): array
    {
        $results = [];
        foreach (array_chunk($texts, self::BATCH_SIZE) as $batch) {
            foreach ($batch as $text) {
                $results[] = [
                    'text' => $text,
                    'vector' => $this->embed($text, $model),
                ];
            }
        }
        return $results;
    }

    public function storeEmbedding(string $content, string $embeddingableType, string $embeddingableId, array $vector): Embedding
    {
        return Embedding::updateOrCreate(
            [
                'embeddingable_type' => $embeddingableType,
                'embeddingable_id' => $embeddingableId,
            ],
            [
                'content' => mb_substr($content, 0, 500),
                'embedding_vector' => $vector,
                'model' => 'text-embedding-004',
                'dimensions' => count($vector),
                'token_count' => (int) ceil(mb_strlen($content) / 4),
            ]
        );
    }

    public function cosineSimilarity(array $a, array $b): float
    {
        if (empty($a) || empty($b) || count($a) !== count($b)) return 0;

        $dot = 0;
        $normA = 0;
        $normB = 0;

        foreach ($a as $i => $val) {
            $dot += $val * $b[$i];
            $normA += $val * $val;
            $normB += $b[$i] * $b[$i];
        }

        $denom = sqrt($normA) * sqrt($normB);
        return $denom > 0 ? $dot / $denom : 0;
    }

    public function searchSimilar(string $query, int $limit = 5, float $minScore = 0.3): array
    {
        $queryVector = $this->embed($query);

        $embeddings = Embedding::where('dimensions', '>', 0)->get();
        $scored = [];

        foreach ($embeddings as $emb) {
            $vector = $emb->embedding_vector;
            if (empty($vector)) continue;

            $score = $this->cosineSimilarity($queryVector, $vector);
            if ($score >= $minScore) {
                $scored[] = [
                    'embedding' => $emb,
                    'score' => $score,
                    'content' => $emb->content,
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($scored, 0, $limit);
    }

    private function fallbackEmbed(string $content): array
    {
        $hash = md5($content);
        $dim = self::DIMENSIONS;
        $vector = [];
        for ($i = 0; $i < $dim; $i++) {
            $val = (hexdec(substr($hash, $i % 32, 2)) / 255) * 2 - 1;
            $vector[] = $val + (sin($i * 0.1) * 0.01);
        }
        return $vector;
    }
}
