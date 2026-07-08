<?php

namespace App\Core\Popup\Repositories;

use App\Core\Popup\DTOs\PopupDTO;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupRule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopupRepository
{
    public function __construct(
        private readonly Popup $model,
        private readonly PopupRule $ruleModel,
    ) {}

    public function findById(string $id): ?Popup
    {
        return $this->model->with([
            'category', 'template', 'rules', 'triggers',
            'displayRules', 'targetingRules', 'schedules',
            'abTest.variants',
        ])->find($id);
    }

    public function findBySlug(string $slug): ?Popup
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getAllActive(): Collection
    {
        return Cache::remember('popups:active', config('popup-builder.cache.ttl', 3600), function () {
            return $this->model
                ->with(['rules', 'triggers', 'displayRules', 'targetingRules'])
                ->visible()
                ->byPriority()
                ->get();
        });
    }

    public function getByType(string $type): Collection
    {
        return $this->model->byType($type)->visible()->byPriority()->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with('category')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function create(PopupDTO $dto): Popup
    {
        return DB::transaction(function () use ($dto) {
            return $this->model->create($dto->toArray());
        });
    }

    public function update(Popup $popup, PopupDTO $dto): Popup
    {
        DB::transaction(function () use ($popup, $dto) {
            $popup->update($dto->toArray());
        });
        $this->clearCache($popup->id);
        return $popup->fresh();
    }

    public function delete(Popup $popup): bool
    {
        $result = $popup->delete();
        $this->clearCache($popup->id);
        return $result;
    }

    public function restore(string $id): ?Popup
    {
        $popup = $this->model->withTrashed()->find($id);
        if ($popup) {
            $popup->restore();
            $this->clearCache($id);
        }
        return $popup;
    }

    public function duplicate(Popup $popup): Popup
    {
        return DB::transaction(function () use ($popup) {
            $clone = $popup->replicate();
            $clone->title = $popup->title . ' (Copy)';
            $clone->slug = $popup->slug . '-copy-' . uniqid();
            $clone->status = 'draft';
            $clone->save();

            foreach ($popup->rules as $rule) {
                $clone->rules()->create($rule->toArray());
            }

            return $clone;
        });
    }

    public function updateStatus(Popup $popup, string $status): Popup
    {
        $popup->update(['status' => $status]);
        $this->clearCache($popup->id);
        return $popup->fresh();
    }

    public function updateCounters(Popup $popup, array $counters): void
    {
        $popup->incrementEach($counters);
        $this->clearCache($popup->id);
    }

    private function clearCache(string $popupId): void
    {
        Cache::forget('popups:active');
        Cache::forget("popup:{$popupId}");
    }
}
