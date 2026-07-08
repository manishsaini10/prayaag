<?php

namespace App\Core\Concerns;

use App\Models\ActivityLog;

/**
 * Lightweight audit trail. Writes an activity_logs row on
 * create / update / delete. Swap for spatie/laravel-activitylog
 * later if you want richer features; the table shape is compatible.
 */
trait RecordsActivity
{
    public static function bootRecordsActivity(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function ($model) use ($event): void {
                $model->recordActivity($event);
            });
        }
    }

    public function recordActivity(string $event): void
    {
        $user = auth()->user();

        ActivityLog::create([
            'log_name'     => $this->getTable(),
            'description'  => class_basename($this) . ' ' . $event,
            'subject_type' => get_class($this),
            'subject_id'   => $this->getKey(),
            'causer_type'  => $user?->getMorphClass(),
            'causer_id'    => $user?->getKey(),
            'properties'   => $this->activityProperties($event),
        ]);
    }

    protected function activityProperties(string $event): array
    {
        return match ($event) {
            'updated' => ['old' => $this->getOriginal(), 'attributes' => $this->getChanges()],
            'deleted' => ['attributes' => $this->getOriginal()],
            default   => ['attributes' => $this->getAttributes()],
        };
    }
}
