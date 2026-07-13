<?php

namespace App\Core\Chatbot\Services;

use App\Models\Chatbot\Enterprise\Automation;
use App\Models\Chatbot\Enterprise\AutomationLog;
use App\Models\Chatbot\Enterprise\Ticket;
use App\Models\Chatbot\Enterprise\Deal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AutomationService
{
    public function matchAndExecute(string $event, array $context = []): void
    {
        $automations = Automation::active()
            ->where('trigger_type', $event)
            ->orderBy('priority')
            ->get();

        foreach ($automations as $automation) {
            $this->execute($automation, $event, $context);
        }
    }

    public function execute(Automation $automation, string $event, array $context = []): void
    {
        $start = microtime(true);

        if (!$automation->canExecute()) return;

        $conditionsMet = $this->evaluateConditions($automation->conditions ?? [], $context);

        $actions = $automation->actions ?? [];
        $executed = [];
        $error = null;

        if ($conditionsMet) {
            foreach ($actions as $action) {
                try {
                    $result = $this->runAction($action, $context);
                    $executed[] = ['action' => $action['type'] ?? 'unknown', 'result' => $result, 'success' => true];
                } catch (\Throwable $e) {
                    $executed[] = ['action' => $action['type'] ?? 'unknown', 'error' => $e->getMessage(), 'success' => false];
                    $error = $e->getMessage();
                    Log::error("Automation action failed: {$e->getMessage()}", [
                        'automation_id' => $automation->id,
                        'action' => $action['type'] ?? 'unknown',
                    ]);
                }
            }
        }

        $duration = (int) ((microtime(true) - $start) * 1000);

        AutomationLog::create([
            'automation_id' => $automation->id,
            'trigger_event' => $event,
            'context' => $context,
            'conditions_met' => $conditionsMet,
            'executed_actions' => $executed,
            'status' => $error ? 'failed' : 'completed',
            'error_message' => $error,
            'duration_ms' => $duration,
        ]);

        $automation->increment('execution_count');
        $automation->update(['last_run_at' => now()]);
    }

    public function evaluateConditions(array $conditions, array $context): bool
    {
        if (empty($conditions)) return true;

        $operator = $conditions['operator'] ?? 'and';
        $rules = $conditions['rules'] ?? [];

        if (empty($rules)) return true;

        $results = [];

        foreach ($rules as $rule) {
            $field = $rule['field'] ?? '';
            $op = $rule['operator'] ?? 'eq';
            $value = $rule['value'] ?? null;
            $actual = data_get($context, $field);

            $results[] = match ($op) {
                'eq' => $actual == $value,
                'neq' => $actual != $value,
                'gt' => is_numeric($actual) && $actual > $value,
                'gte' => is_numeric($actual) && $actual >= $value,
                'lt' => is_numeric($actual) && $actual < $value,
                'lte' => is_numeric($actual) && $actual <= $value,
                'contains' => is_string($actual) && str_contains($actual, $value ?? ''),
                'not_contains' => is_string($actual) && !str_contains($actual, $value ?? ''),
                'in' => in_array($actual, (array) $value),
                'not_in' => !in_array($actual, (array) $value),
                'starts_with' => is_string($actual) && str_starts_with($actual, $value ?? ''),
                'ends_with' => is_string($actual) && str_ends_with($actual, $value ?? ''),
                'is_empty' => empty($actual),
                'not_empty' => !empty($actual),
                default => true,
            };
        }

        return $operator === 'and' ? !in_array(false, $results, true) : in_array(true, $results, true);
    }

    public function runAction(array $action, array $context): mixed
    {
        $type = $action['type'] ?? '';
        $config = $action['config'] ?? [];

        return match ($type) {
            'send_email' => $this->actionSendEmail($config, $context),
            'send_notification' => $this->actionSendNotification($config, $context),
            'create_ticket' => $this->actionCreateTicket($config, $context),
            'update_ticket' => $this->actionUpdateTicket($config, $context),
            'assign_ticket' => $this->actionAssignTicket($config, $context),
            'move_deal' => $this->actionMoveDeal($config, $context),
            'update_deal' => $this->actionUpdateDeal($config, $context),
            'send_webhook' => $this->actionSendWebhook($config, $context),
            'send_sms' => $this->actionSendSms($config, $context),
            'add_tag' => $this->actionAddTag($config, $context),
            'log_message' => $this->actionLogMessage($config, $context),
            default => throw new \InvalidArgumentException("Unknown action type: {$type}"),
        };
    }

    protected function actionSendEmail(array $config, array $context): array
    {
        $to = $this->resolvePlaceholders($config['to'] ?? '', $context);
        $subject = $this->resolvePlaceholders($config['subject'] ?? '', $context);
        $body = $this->resolvePlaceholders($config['body'] ?? '', $context);

        Log::info("Automation would send email to {$to}: {$subject}");

        return ['to' => $to, 'subject' => $subject, 'status' => 'queued'];
    }

    protected function actionSendNotification(array $config, array $context): array
    {
        $message = $this->resolvePlaceholders($config['message'] ?? '', $context);
        $channels = $config['channels'] ?? ['database'];

        Log::info("Automation notification: {$message}");

        return ['message' => $message, 'channels' => $channels, 'status' => 'sent'];
    }

    protected function actionCreateTicket(array $config, array $context): array
    {
        $ticket = Ticket::create([
            'subject' => $this->resolvePlaceholders($config['subject'] ?? 'Auto-generated ticket', $context),
            'description' => $this->resolvePlaceholders($config['description'] ?? '', $context),
            'priority' => $config['priority'] ?? 'medium',
            'status' => 'open',
            'department_id' => $config['department_id'] ?? null,
            'assigned_agent_id' => $config['assigned_agent_id'] ?? null,
        ]);

        return ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number];
    }

    protected function actionUpdateTicket(array $config, array $context): array
    {
        $ticketId = $config['ticket_id'] ?? $context['ticket_id'] ?? null;
        if (!$ticketId) return ['error' => 'No ticket ID provided'];

        $ticket = Ticket::find($ticketId);
        if (!$ticket) return ['error' => 'Ticket not found'];

        $updates = [];
        foreach (['status', 'priority', 'department_id'] as $field) {
            if (isset($config[$field])) {
                $updates[$field] = $this->resolvePlaceholders((string) $config[$field], $context);
            }
        }
        $ticket->update($updates);

        return ['ticket_id' => $ticket->id, 'updates' => $updates];
    }

    protected function actionAssignTicket(array $config, array $context): array
    {
        $ticketId = $config['ticket_id'] ?? $context['ticket_id'] ?? null;
        if (!$ticketId) return ['error' => 'No ticket ID provided'];

        $ticket = Ticket::find($ticketId);
        if (!$ticket) return ['error' => 'Ticket not found'];

        $agentId = $config['agent_id'] ?? null;
        if (!$agentId) return ['error' => 'No agent ID provided'];

        $ticket->update(['assigned_agent_id' => $agentId]);

        return ['ticket_id' => $ticket->id, 'assigned_agent_id' => $agentId];
    }

    protected function actionMoveDeal(array $config, array $context): array
    {
        $dealId = $config['deal_id'] ?? $context['deal_id'] ?? null;
        if (!$dealId) return ['error' => 'No deal ID provided'];

        $deal = Deal::find($dealId);
        if (!$deal) return ['error' => 'Deal not found'];

        $stageId = $config['stage_id'] ?? null;
        if (!$stageId) return ['error' => 'No stage ID provided'];

        $deal->update(['stage_id' => $stageId]);

        return ['deal_id' => $deal->id, 'stage_id' => $stageId];
    }

    protected function actionUpdateDeal(array $config, array $context): array
    {
        $dealId = $config['deal_id'] ?? $context['deal_id'] ?? null;
        if (!$dealId) return ['error' => 'No deal ID provided'];

        $deal = Deal::find($dealId);
        if (!$deal) return ['error' => 'Deal not found'];

        $updates = [];
        foreach (['value', 'status', 'expected_close_date'] as $field) {
            if (isset($config[$field])) {
                $updates[$field] = $config[$field];
            }
        }
        $deal->update($updates);

        return ['deal_id' => $deal->id, 'updates' => $updates];
    }

    protected function actionSendWebhook(array $config, array $context): array
    {
        $url = $this->resolvePlaceholders($config['url'] ?? '', $context);
        $method = strtoupper($config['method'] ?? 'POST');
        $headers = $config['headers'] ?? [];
        $body = $config['body'] ?? $context;

        if (empty($url)) return ['error' => 'No webhook URL provided'];

        $response = Http::withHeaders($headers)->send($method, $url, ['json' => $body]);

        return ['url' => $url, 'method' => $method, 'status' => $response->status()];
    }

    protected function actionSendSms(array $config, array $context): array
    {
        $to = $this->resolvePlaceholders($config['to'] ?? '', $context);
        $message = $this->resolvePlaceholders($config['message'] ?? '', $context);

        Log::info("Automation would send SMS to {$to}: {$message}");

        return ['to' => $to, 'message' => $message, 'status' => 'queued'];
    }

    protected function actionAddTag(array $config, array $context): array
    {
        $tag = $config['tag'] ?? '';
        $target = $config['target'] ?? 'ticket';
        $targetId = $context["{$target}_id"] ?? null;

        Log::info("Automation would add tag '{$tag}' to {$target}#{$targetId}");

        return ['tag' => $tag, 'target' => $target, 'target_id' => $targetId];
    }

    protected function actionLogMessage(array $config, array $context): array
    {
        $message = $this->resolvePlaceholders($config['message'] ?? '', $context);
        $level = $config['level'] ?? 'info';

        Log::{$level}("Automation log: {$message}");

        return ['message' => $message, 'level' => $level];
    }

    protected function resolvePlaceholders(string $text, array $context): string
    {
        return preg_replace_callback('/\{\{(\w+(?:\.\w+)*)\}\}/', function ($m) use ($context) {
            return data_get($context, $m[1], $m[0]);
        }, $text);
    }
}
