<?php

namespace App\View\Components\Popup;

use App\Core\Popup\Engines\RenderingEngine;
use App\Core\Popup\Repositories\PopupRepository;
use App\Core\Popup\Services\RuleEngineService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PopupRender extends Component
{
    public Collection $popups;
    public string $html = '';

    public function __construct(
        private readonly PopupRepository $repository,
        private readonly RenderingEngine $engine,
        private readonly RuleEngineService $ruleEngine,
    ) {
        $this->popups = collect();
    }

    public function render(): View
    {
        try {
            $popups = $this->repository->getAllActive();
            $context = [
                'url' => request()->url(),
                'path' => request()->path(),
                'user_id' => auth()->id(),
                'is_guest' => auth()->guest() ? 'true' : 'false',
                'is_logged_in' => auth()->check() ? 'true' : 'false',
            ];

            foreach ($popups as $popup) {
                if ($this->ruleEngine->evaluate($popup, $context)) {
                    $popup->load(['triggers', 'displayRules', 'targetingRules']);
                    $triggers = $this->ruleEngine->evaluateTriggers($popup);
                    $popup->trigger_config = $triggers;
                    $this->popups->push($popup);
                }
            }

            if ($this->popups->isNotEmpty()) {
                $html = '';
                foreach ($this->popups as $popup) {
                    $popup->settings['trigger'] = $popup->trigger_config->first()['key'] ?? 'page_load';
                    $popup->settings['delay'] = $popup->trigger_config->first()['value'] ?? 0;
                    $popup->settings['clickSelector'] = $popup->trigger_config->first()['extra']['selector'] ?? '';
                    $html .= $this->engine->render($popup);
                }
                $this->html = $html;
            }
        } catch (\Exception $e) {
            report($e);
        }

        return view('components.popup.render-output', [
            'html' => $this->html,
            'popups' => $this->popups,
        ]);
    }
}
