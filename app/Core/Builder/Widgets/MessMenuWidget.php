<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Mess\Services\MessMenuService;
use Illuminate\Support\Carbon;

class MessMenuWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'mess_menu';
    }

    public function label(): string
    {
        return 'Mess Menu';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Weekly Mess Menu',
            'display_mode' => 'weekly_grid',
            'show_special_days' => true,
            'highlight_today' => true,
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'display_mode' => ['weekly_grid', 'today_only', 'tabs'],
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $service = app(MessMenuService::class);
        $data = $service->getActiveMenuGrouped();
        
        $menu = $data['menu'] ?? null;
        $grouped = $data['grouped'] ?? [];

        $schedule = $data['schedule'] ?? [];

        // Check for today's special day override
        $specialOverrides = [];
        if ($this->setting($settings, 'show_special_days', true)) {
            $todayDate = Carbon::now()->format('Y-m-d');
            $specialOverrides = $service->getSpecialOverrideForDate($todayDate);
        }

        return view('widgets.mess-menu', [
            'menu' => $menu,
            'grouped' => $grouped,
            'schedule' => $schedule,
            'specialOverrides' => $specialOverrides,
            'settings' => $settings,
            'title' => $this->setting($settings, 'title', 'Weekly Mess Menu'),
            'displayMode' => $this->setting($settings, 'display_mode', 'weekly_grid'),
            'highlightToday' => $this->setting($settings, 'highlight_today', true),
        ])->render();
    }
}
