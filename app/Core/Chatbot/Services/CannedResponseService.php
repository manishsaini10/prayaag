<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\CannedResponse;
use Illuminate\Support\Collection;

/**
 * Handles canned response lookup and AI-powered suggestion.
 * Suggestions are rule-based (intent + keyword match) to avoid extra LLM cost.
 */
class CannedResponseService
{
    /**
     * Search canned responses by shortcut or body keyword.
     * Used when agent types "/" in the reply box.
     */
    public function search(string $query, int $limit = 8): Collection
    {
        if (empty(trim($query))) {
            return CannedResponse::orderBy('shortcut')->limit($limit)->get();
        }

        return CannedResponse::search($query)->orderBy('shortcut')->limit($limit)->get();
    }

    /**
     * Suggest top matching canned responses based on user message + intent.
     * Uses keyword overlap scoring (no extra LLM call needed).
     */
    public function suggest(string $userMessage, string $intent = 'general', int $limit = 3): Collection
    {
        $all = CannedResponse::all();
        $lower = strtolower($userMessage);

        // Score each canned response by keyword overlap with user message
        $scored = $all->map(function ($cr) use ($lower, $intent) {
            $score = 0;

            // Category match with intent gives bonus
            if ($cr->category && strtolower($cr->category) === $intent) {
                $score += 5;
            }

            // Word overlap between user message and body
            $bodyWords = preg_split('/\s+/', strtolower(strip_tags($cr->body)));
            $msgWords  = preg_split('/\s+/', $lower);

            $overlap = count(array_intersect(
                array_filter($bodyWords, fn($w) => strlen($w) > 3),
                array_filter($msgWords,  fn($w) => strlen($w) > 3)
            ));
            $score += $overlap;

            // Shortcut keyword presence in message boosts strongly
            if ($cr->shortcut && str_contains($lower, strtolower($cr->shortcut))) {
                $score += 10;
            }

            $cr->_score = $score;
            return $cr;
        })
        ->filter(fn($cr) => $cr->_score > 0)
        ->sortByDesc('_score')
        ->take($limit)
        ->values();

        return $scored;
    }

    /**
     * Get all responses grouped by category for admin UI.
     */
    public function grouped(): array
    {
        return CannedResponse::orderBy('category')->orderBy('shortcut')
            ->get()
            ->groupBy(fn($r) => $r->category ?? 'General')
            ->toArray();
    }

    /**
     * Create a new canned response.
     */
    public function create(array $data): CannedResponse
    {
        return CannedResponse::create($data);
    }

    /**
     * Update an existing canned response.
     */
    public function update(CannedResponse $cr, array $data): CannedResponse
    {
        $cr->update($data);
        return $cr->fresh();
    }

    /**
     * Delete a canned response.
     */
    public function delete(CannedResponse $cr): void
    {
        $cr->delete();
    }
}
