<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\ChatbotConversation;

class ConversationMemory
{
    private const SUMMARY_THRESHOLD = 10;

    public function needsSummarization(ChatbotConversation $conversation): bool
    {
        $count = $conversation->messages()->count();
        return $count >= self::SUMMARY_THRESHOLD && empty($conversation->memory_summary);
    }

    public function buildContext(ChatbotConversation $conversation): string
    {
        $summary = $conversation->memory_summary;
        if ($summary) {
            $recentMessages = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->reverse();

            $recent = '';
            foreach ($recentMessages as $msg) {
                $role = $msg->sender_type === 'visitor' ? 'User' : 'Assistant';
                $recent .= "$role: {$msg->message_text}\n";
            }

            return "Conversation Summary:\n{$summary}\n\nRecent Messages:\n{$recent}";
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        $context = '';
        foreach ($messages as $msg) {
            $role = $msg->sender_type === 'visitor' ? 'User' : 'Assistant';
            $context .= "$role: {$msg->message_text}\n";
        }

        return $context;
    }

    public function generateSummary(ChatbotConversation $conversation): string
    {
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        $count = $messages->count();
        $userMessages = $messages->where('sender_type', 'visitor')->values();
        $botMessages = $messages->where('sender_type', 'chatbot')->values();

        $topics = [];
        foreach ($userMessages as $msg) {
            $lower = strtolower($msg->message_text);
            if (str_contains($lower, 'admission') || str_contains($lower, 'apply')) $topics['Admission'] = true;
            if (str_contains($lower, 'fee') || str_contains($lower, 'cost') || str_contains($lower, 'payment')) $topics['Fee'] = true;
            if (str_contains($lower, 'contact') || str_contains($lower, 'phone') || str_contains($lower, 'email')) $topics['Contact'] = true;
            if (str_contains($lower, 'curriculum') || str_contains($lower, 'subject') || str_contains($lower, 'class')) $topics['Academic'] = true;
            if (str_contains($lower, 'facility') || str_contains($lower, 'lab') || str_contains($lower, 'sport')) $topics['Facility'] = true;
            if (str_contains($lower, 'complaint') || str_contains($lower, 'issue') || str_contains($lower, 'problem')) $topics['Complaint'] = true;
        }

        $topicList = empty($topics) ? 'General inquiry' : implode(', ', array_keys($topics));
        $userName = $conversation->visitor?->name ?? 'Visitor';

        $botSummary = '';
        foreach ($botMessages as $msg) {
            $botSummary .= " - " . substr($msg->message_text, 0, 100) . "\n";
        }

        return "Conversation with {$userName} ({$count} messages). Topics discussed: {$topicList}. Key information shared:\n{$botSummary}";
    }

    public function summarize(ChatbotConversation $conversation): void
    {
        if (!$this->needsSummarization($conversation)) return;
        $summary = $this->generateSummary($conversation);
        $conversation->update(['memory_summary' => $summary]);
    }
}
