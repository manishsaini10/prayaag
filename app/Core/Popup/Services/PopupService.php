<?php

namespace App\Core\Popup\Services;

use App\Core\Popup\DTOs\PopupDTO;
use App\Core\Popup\DTOs\RuleDTO;
use App\Core\Popup\Events\PopupCreated;
use App\Core\Popup\Events\PopupDeleted;
use App\Core\Popup\Events\PopupStatusChanged;
use App\Core\Popup\Events\PopupUpdated;
use App\Core\Popup\Repositories\PopupRepository;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupActivityLog;
use App\Models\Popup\PopupRevision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopupService
{
    public function __construct(
        private readonly PopupRepository $repository,
        private readonly RuleEngineService $ruleEngine,
        private readonly TemplateService $templateService,
    ) {}

    public function create(PopupDTO $dto, ?string $fromTemplate = null): Popup
    {
        if ($fromTemplate) {
            $template = $this->templateService->find($fromTemplate);
            if ($template) {
                $dto = PopupDTO::fromArray(array_merge($dto->toArray(), [
                    'structure' => $template->structure,
                    'settings' => $template->settings ?? [],
                    'styles' => $template->styles ?? [],
                ]));
            }
        }

        $popup = $this->repository->create($dto);

        $this->logActivity($popup, 'created', 'Popup created');
        $this->createRevision($popup, 'Initial version');

        event(new PopupCreated($popup));

        return $popup;
    }

    public function update(Popup $popup, PopupDTO $dto): Popup
    {
        $oldStatus = $popup->status;
        $popup = $this->repository->update($popup, $dto);

        $this->logActivity($popup, 'updated', 'Popup updated');
        $this->createRevision($popup, 'Updated via editor');

        if ($oldStatus !== $dto->status) {
            event(new PopupStatusChanged($popup, $oldStatus, $dto->status));
        }

        event(new PopupUpdated($popup));

        return $popup;
    }

    public function delete(Popup $popup): bool
    {
        $this->logActivity($popup, 'deleted', 'Popup deleted');
        $result = $this->repository->delete($popup);
        event(new PopupDeleted($popup));
        return $result;
    }

    public function duplicate(Popup $popup): Popup
    {
        $clone = $this->repository->duplicate($popup);
        $this->logActivity($clone, 'duplicated', "Duplicated from {$popup->title}");
        return $clone;
    }

    public function updateStatus(Popup $popup, string $status): Popup
    {
        $oldStatus = $popup->status;
        $popup = $this->repository->updateStatus($popup, $status);
        $this->logActivity($popup, 'status_changed', "Status changed from {$oldStatus} to {$status}");
        event(new PopupStatusChanged($popup, $oldStatus, $status));
        return $popup;
    }

    public function publish(Popup $popup): Popup
    {
        return $this->updateStatus($popup, 'active');
    }

    public function unpublish(Popup $popup): Popup
    {
        return $this->updateStatus($popup, 'paused');
    }

    public function addRule(Popup $popup, RuleDTO $dto): void
    {
        $popup->rules()->create($dto->toArray());
        $this->repository->clearCache($popup->id);
    }

    public function updateRules(Popup $popup, string $type, array $rules): void
    {
        DB::transaction(function () use ($popup, $type, $rules) {
            $popup->rules()->where('type', $type)->delete();
            foreach ($rules as $index => $rule) {
                $rule['type'] = $type;
                $rule['sort_order'] = $index;
                $popup->rules()->create($rule);
            }
        });
        $this->repository->clearCache($popup->id);
    }

    public function getRevisions(Popup $popup, int $limit = 50)
    {
        return $popup->revisions()
            ->with('creator:id,name')
            ->orderBy('version', 'desc')
            ->limit($limit)
            ->get();
    }

    public function restoreRevision(Popup $popup, int $version): Popup
    {
        $revision = $popup->revisions()->where('version', $version)->firstOrFail();
        $popup->update([
            'structure' => $revision->structure,
            'settings' => $revision->settings,
            'design' => $revision->design,
        ]);
        $this->logActivity($popup, 'revision_restored', "Restored to version {$version}");
        $this->createRevision($popup, "Restored from version {$version}");
        return $popup->fresh();
    }

    private function logActivity(Popup $popup, string $action, string $description): void
    {
        PopupActivityLog::create([
            'popup_id' => $popup->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'causer_id' => Auth::id(),
        ]);
    }

    private function createRevision(Popup $popup, string $note): void
    {
        $latestVersion = $popup->revisions()->max('version') ?? 0;
        PopupRevision::create([
            'popup_id' => $popup->id,
            'version' => $latestVersion + 1,
            'note' => $note,
            'structure' => $popup->structure,
            'settings' => $popup->settings,
            'design' => $popup->design,
            'created_by' => Auth::id(),
        ]);
    }
}
