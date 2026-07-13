<?php

namespace App\Providers;

use App\Core\Chatbot\Services\ChatbotAIService;
use App\Core\Chatbot\Services\ChatbotRAGService;
use App\Core\Chatbot\Services\ConversationMemory;
use App\Core\Chatbot\Services\ConfidenceScorer;
use App\Core\Chatbot\Services\MultiLLMRouter;
use App\Core\Chatbot\Services\VisitorTrackingService;
use App\Core\Chatbot\Services\AutomationService;
use App\Core\Chatbot\Repositories\ChatbotRepository;
use Illuminate\Support\ServiceProvider;

class ChatbotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChatbotAIService::class, fn () => ChatbotAIService::make());
        $this->app->singleton(ChatbotRAGService::class);
        $this->app->singleton(ChatbotRepository::class);
        $this->app->singleton(VisitorTrackingService::class);
        $this->app->singleton(AutomationService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/chatbot.php', 'chatbot'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/chatbot.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/chatbot.php' => config_path('chatbot.php'),
            ], 'chatbot-config');
        }
    }
}
