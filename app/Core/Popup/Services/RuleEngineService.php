<?php

namespace App\Core\Popup\Services;

use App\Models\Popup\Popup;
use App\Models\Popup\PopupRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class RuleEngineService
{
    public function evaluate(Popup $popup, array $context = []): bool
    {
        if (! $this->evaluateDisplayRules($popup, $context)) return false;
        if (! $this->evaluateTargetingRules($popup, $context)) return false;
        if (! $this->evaluateFrequencyRules($popup, $context)) return false;
        return true;
    }

    public function evaluateDisplayRules(Popup $popup, array $context): bool
    {
        $rules = $popup->displayRules;
        if ($rules->isEmpty()) return true;

        foreach ($rules as $rule) {
            if (! $this->evaluateSingleRule($rule, $context)) {
                return false;
            }
        }
        return true;
    }

    public function evaluateTargetingRules(Popup $popup, array $context): bool
    {
        $rules = $popup->targetingRules;
        if ($rules->isEmpty()) return true;

        $includeRules = $rules->where('condition', 'is');
        $excludeRules = $rules->where('condition', 'is_not');

        // Exclude rules: if ANY matches, block immediately
        foreach ($excludeRules as $rule) {
            if (! $this->evaluateSingleRule($rule, $context)) {
                return false;
            }
        }

        // Include rules: if ANY matches, allow; if NONE match, block
        if ($includeRules->isNotEmpty()) {
            foreach ($includeRules as $rule) {
                if ($this->evaluateSingleRule($rule, $context)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    public function evaluateFrequencyRules(Popup $popup, array $context): bool
    {
        $type = $popup->frequency_type;
        return match ($type) {
            'every_visit' => true,
            'once_per_session' => ! session("popup_shown_{$popup->id}", false),
            'once_per_day' => ! cache("popup_day_{$popup->id}_" . date('Ymd')),
            'weekly' => ! cache("popup_week_{$popup->id}_" . date('oW')),
            'monthly' => ! cache("popup_month_{$popup->id}_" . date('Ym')),
            'once_only' => ! cache("popup_once_{$popup->id}"),
            'after_x_days' => $this->evaluateAfterXDays($popup),
            'never_again' => Cookie::has("popup_never_{$popup->id}"),
            'cookie_based' => ! Cookie::has("popup_{$popup->id}"),
            'database_based' => ! cache("popup_user_{$popup->id}_" . ($context['user_id'] ?? 'guest')),
            default => true,
        };
    }

    public function evaluateTriggers(Popup $popup, array $context = []): Collection
    {
        return $popup->triggers->map(function ($rule) {
            return [
                'key' => $rule->rule_key,
                'condition' => $rule->condition,
                'value' => $rule->value,
                'extra' => $rule->extra,
            ];
        });
    }

    public function markShown(Popup $popup): void
    {
        $type = $popup->frequency_type;
        switch ($type) {
            case 'once_per_session':
                session(["popup_shown_{$popup->id}" => true]);
                break;
            case 'once_per_day':
                cache(["popup_day_{$popup->id}_" . date('Ymd') => true, now()->endOfDay()]);
                break;
            case 'weekly':
                cache(["popup_week_{$popup->id}_" . date('oW') => true, now()->endOfWeek()]);
                break;
            case 'monthly':
                cache(["popup_month_{$popup->id}_" . date('Ym') => true, now()->endOfMonth()]);
                break;
            case 'once_only':
                cache(["popup_once_{$popup->id}" => true, now()->addYears(10)]);
                break;
            case 'after_x_days':
                cache(["popup_last_shown_{$popup->id}" => now(), now()->addDays($popup->frequency_x_days ?? 30)]);
                break;
            case 'never_again':
                Cookie::queue("popup_never_{$popup->id}", true, 525600);
                break;
            case 'cookie_based':
                Cookie::queue("popup_{$popup->id}", true, 43200);
                break;
            case 'database_based':
                cache(["popup_user_{$popup->id}_" . (auth()->id() ?? 'guest') => true, now()->addDay()]);
                break;
        }
    }

    private function evaluateSingleRule(PopupRule $rule, array $context): bool
    {
        $contextValue = $context[$rule->rule_key] ?? $this->getContextValue($rule->rule_key, $context);
        $ruleValue = $rule->value;

        if ($rule->rule_key === 'path') {
            return $this->evaluatePathRule((string) $contextValue, (string) $ruleValue, (string) $rule->condition);
        }

        return match ($rule->condition) {
            'is' => $contextValue == $ruleValue,
            'is_not' => $contextValue != $ruleValue,
            'contains' => is_string($contextValue) && str_contains($contextValue, $ruleValue),
            'not_contains' => is_string($contextValue) && ! str_contains($contextValue, $ruleValue),
            'greater_than' => $contextValue > $ruleValue,
            'less_than' => $contextValue < $ruleValue,
            'between' => $this->evaluateBetween($contextValue, $ruleValue),
            'in' => in_array($contextValue, (array) json_decode($ruleValue, true) ?? [$ruleValue]),
            'not_in' => ! in_array($contextValue, (array) json_decode($ruleValue, true) ?? [$ruleValue]),
            'regex' => is_string($contextValue) && preg_match($ruleValue, $contextValue),
            'starts_with' => is_string($contextValue) && str_starts_with($contextValue, $ruleValue),
            'ends_with' => is_string($contextValue) && str_ends_with($contextValue, $ruleValue),
            'exists' => ! is_null($contextValue),
            'not_exists' => is_null($contextValue),
            default => true,
        };
    }

    private function evaluateBetween($value, $ruleValue): bool
    {
        $range = json_decode($ruleValue, true);
        if (! is_array($range) || count($range) < 2) return false;
        return $value >= $range[0] && $value <= $range[1];
    }

    private function evaluatePathRule(string $contextPath, string $rulePath, string $condition): bool
    {
        $matches = $this->pathMatches($contextPath, $rulePath);

        return match ($condition) {
            'is' => $matches,
            'is_not' => ! $matches,
            'contains' => str_contains($this->normalizePath($contextPath), trim($rulePath, '/ ')),
            'not_contains' => ! str_contains($this->normalizePath($contextPath), trim($rulePath, '/ ')),
            'starts_with' => str_starts_with($this->normalizePath($contextPath), trim($rulePath, '/ ')),
            'ends_with' => str_ends_with($this->normalizePath($contextPath), trim($rulePath, '/ ')),
            default => $matches,
        };
    }

    private function pathMatches(string $contextPath, string $rulePath): bool
    {
        $contextPath = $this->normalizePath($contextPath);
        $rulePath = $this->normalizePath($rulePath);

        if ($rulePath === '*') {
            return true;
        }

        if ($contextPath === $rulePath) {
            return true;
        }

        if (str_contains($rulePath, '*')) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($rulePath, '/')) . '$/i';
            return (bool) preg_match($pattern, $contextPath);
        }

        return false;
    }

    private function normalizePath(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '' || $path === '/' || $path === 'home' || $path === '/home') {
            return '/';
        }

        $parsedPath = parse_url($path, PHP_URL_PATH);
        if (is_string($parsedPath) && $parsedPath !== '') {
            $path = $parsedPath;
        }

        $path = trim($path, '/ ');

        return $path === '' ? '/' : $path;
    }

    private function evaluateAfterXDays(Popup $popup): bool
    {
        $days = $popup->frequency_x_days;
        if (! $days) return true;
        $lastShown = cache("popup_last_shown_{$popup->id}");
        if (! $lastShown) return true;
        return now()->diffInDays($lastShown) >= $days;
    }

    private function isMobileDevice(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        return (bool) preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|IEMobile|Opera Mini/i', $userAgent);
    }

    private function getContextValue(string $key, array $context = []): mixed
    {
        return match ($key) {
            'url' => $context['url'] ?? request()->url(),
            'path' => $this->normalizePath($context['path'] ?? request()->path()),
            'homepage' => $this->normalizePath($context['path'] ?? request()->path()) === '/' ? 'true' : 'false',
            'full_url' => $context['full_url'] ?? request()->fullUrl(),
            'query_string' => $context['query_string'] ?? request()->getQueryString(),
            'referrer' => $context['referrer'] ?? request()->header('referer'),
            'user_agent' => $context['user_agent'] ?? request()->userAgent(),
            'ip_address' => $context['ip_address'] ?? request()->ip(),
            'method' => $context['method'] ?? request()->method(),
            'language' => $context['language'] ?? request()->getPreferredLanguage(),
            'is_mobile' => $context['is_mobile'] ?? ($this->isMobileDevice() ? 'true' : 'false'),
            'is_ajax' => $context['is_ajax'] ?? (request()->ajax() ? 'true' : 'false'),
            'is_secure' => $context['is_secure'] ?? (request()->isSecure() ? 'true' : 'false'),
            'session_id' => $context['session_id'] ?? Session::getId(),
            'user_id' => $context['user_id'] ?? auth()->id(),
            'user_role' => $context['user_role'] ?? (auth()->check() ? auth()->user()->roles->pluck('name')->implode(',') : null),
            'is_guest' => $context['is_guest'] ?? (auth()->guest() ? 'true' : 'false'),
            'is_logged_in' => $context['is_logged_in'] ?? (auth()->check() ? 'true' : 'false'),
            default => null,
        };
    }
}
