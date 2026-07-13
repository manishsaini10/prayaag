<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\ChatbotKbDocument;
use App\Models\Chatbot\ChatbotKbChunk;
use App\Models\Page;
use App\Models\Post;
use App\Models\Event;
use App\Models\Chatbot\Enterprise\Embedding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotRAGService
{
    public function __construct(
        private readonly TextEmbeddingService $embeddings = new TextEmbeddingService,
    ) {}

    public function indexCmsContent(): void
    {
        Page::where('status', 'published')->chunk(50, function ($pages) {
            foreach ($pages as $page) {
                $content = "Page Title: " . $page->title . "\nURL Path: /" . ltrim($page->slug, '/') . "\nDescription: " . ($page->meta_description ?? '');
                $this->saveKbDocument("page-{$page->id}", $page->title, 'page', $content);
            }
        });

        Post::where('status', 'published')->chunk(50, function ($posts) {
            foreach ($posts as $post) {
                $content = "Post Title: " . $post->title . "\nSummary: " . ($post->summary ?? '') . "\nBody: " . strip_tags($post->content);
                $this->saveKbDocument("post-{$post->id}", $post->title, 'blog', $content);
            }
        });

        Event::chunk(50, function ($events) {
            foreach ($events as $event) {
                $content = "Event Name: " . $event->title . "\nDate: " . ($event->start_date?->toDateString() ?? '') . "\nDescription: " . strip_tags($event->description ?? '');
                $this->saveKbDocument("event-{$event->id}", $event->title, 'event', $content);
            }
        });
    }

    public function saveKbDocument(string $sourceId, string $title, string $type, string $content, ?string $categoryId = null): ChatbotKbDocument
    {
        $doc = ChatbotKbDocument::updateOrCreate(
            ['source_id' => $sourceId, 'type' => $type],
            [
                'title' => $title,
                'content' => $content,
                'category_id' => $categoryId,
                'is_active' => true,
                'word_count' => str_word_count($content),
                'indexed_at' => now(),
            ]
        );

        $doc->chunks()->delete();
        Embedding::where('embeddingable_type', ChatbotKbChunk::class)
            ->whereIn('embeddingable_id', $doc->chunks()->pluck('id'))
            ->delete();

        $chunks = $this->chunkText($content, $title, $type, $sourceId);

        foreach ($chunks as $chunkData) {
            $chunk = ChatbotKbChunk::create([
                'document_id' => $doc->id,
                'chunk_text' => $chunkData['text'],
                'meta' => [
                    'title' => $title,
                    'type' => $type,
                    'source_id' => $sourceId,
                    'chunk_index' => $chunkData['index'],
                ],
            ]);

            $vector = $this->embeddings->embed($chunkData['text']);
            $this->embeddings->storeEmbedding($chunkData['text'], ChatbotKbChunk::class, $chunk->id, $vector);
        }

        $doc->update(['chunk_count' => count($chunks)]);

        return $doc;
    }

    public function search(string $query, int $limit = 5): array
    {
        $semanticResults = $this->embeddings->searchSimilar($query, $limit, 0.35);

        if (!empty($semanticResults)) {
            return array_map(function ($item) {
                return [
                    'text' => $item['content'],
                    'title' => '',
                    'type' => 'semantic',
                    'score' => round($item['score'], 3),
                ];
            }, $semanticResults);
        }

        return $this->keywordSearch($query, $limit);
    }

    private function keywordSearch(string $query, int $limit = 5): array
    {
        $keywords = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $query)));
        if (empty($keywords)) return [];

        $chunksQuery = ChatbotKbChunk::query()
            ->join('chatbot_kb_documents', 'chatbot_kb_chunks.document_id', '=', 'chatbot_kb_documents.id')
            ->where('chatbot_kb_documents.is_active', true);

        $bindings = [];
        $scoreSql = [];

        foreach ($keywords as $word) {
            if (mb_strlen($word) < 3) continue;
            $scoreSql[] = "CASE WHEN chunk_text LIKE ? THEN 1 ELSE 0 END";
            $bindings[] = '%' . $word . '%';
        }

        if (!empty($scoreSql)) {
            $sql = "(" . implode(' + ', $scoreSql) . ") as match_score";
            $chunksQuery->selectRaw("chatbot_kb_chunks.*, " . $sql, $bindings)->orderBy('match_score', 'desc');
        } else {
            $chunksQuery->select('chatbot_kb_chunks.*');
        }

        return $chunksQuery->limit($limit)->get()->map(function ($chunk) {
            return [
                'text' => $chunk->chunk_text,
                'title' => $chunk->meta['title'] ?? '',
                'type' => $chunk->meta['type'] ?? 'keyword',
                'score' => 0,
            ];
        })->toArray();
    }

    private function chunkText(string $content, string $title, string $type, string $sourceId): array
    {
        $maxSize = 600;
        $overlap = 100;
        $chunks = [];

        $paragraphs = preg_split('/\n\s*\n/', $content);

        $current = '';
        $index = 0;

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (empty($para)) continue;

            if (mb_strlen($current) + mb_strlen($para) > $maxSize && !empty($current)) {
                $chunks[] = ['text' => trim($current), 'index' => $index++];
                $current = mb_substr($current, -$overlap) . "\n";
            }

            $current .= $para . "\n";
        }

        if (!empty(trim($current))) {
            $chunks[] = ['text' => trim($current), 'index' => $index];
        }

        if (empty($chunks)) {
            $chunks[] = ['text' => trim(mb_substr($content, 0, $maxSize)), 'index' => 0];
        }

        return $chunks;
    }
}
