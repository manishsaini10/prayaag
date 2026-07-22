<?php

namespace App\Core\Analytics\Services;

use Carbon\Carbon;

class DateRangeResolver
{
    public static function resolve(string $range, ?string $from = null, ?string $to = null): array
    {
        return match ($range) {
            '7days'  => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
            '30days' => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
            '90days' => [now()->subDays(90)->startOfDay(), now()->endOfDay()],
            'custom' => [
                $from ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay(),
                $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay()
            ],
            default  => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
        };
    }
}
