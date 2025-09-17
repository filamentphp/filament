<?php

namespace Filament\Support\Livewire\Partials;

use Closure;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\ComponentHook;
use Livewire\Drawer\Utils;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Livewire\Mechanisms\HandleComponents\ViewContext;

use function Livewire\store;
use function Livewire\trigger;

class PartialsComponentHook extends ComponentHook
{
    public function shouldSkipRender(): bool
    {
        if (! $this->isLackingPartialRendersToCoverAllCallsAndUpdates()) {
            return true;
        }

        if ($this->shouldRenderMountedActionOnly()) {
            return true;
        }

        if ($this->shouldRenderMountedActionsOnly()) {
            return true;
        }

        return false;
    }

    public function update(): void
    {
        $this->storeSet('updatesCount', ($this->storeGet('updatesCount') ?? 0) + 1);

        $this->storeSet('isPendingPartialRender', true);
    }

    public function call(): void
    {
        $this->storeSet('callsCount', ($this->storeGet('callsCount') ?? 0) + 1);

        $this->storeSet('isPendingPartialRender', true);
    }

    public function isLackingPartialRendersToCoverAllCallsAndUpdates(): bool
    {
        $updatesCount = intval($this->storeGet('updatesCount') ?? 0);
        $callsCount = intval($this->storeGet('callsCount') ?? 0);

        if (($updatesCount + $callsCount) === 0) {
            return true;
        }

        return ($updatesCount + $callsCount) !== intval($this->storeGet('partialRendersCount') ?? 0);
    }

    public function shouldRenderMountedActionOnly(): bool
    {
        if (! property_exists($this->component, 'mountedActions')) {
            return false;
        }

        $originallyMountedActionIndex = $this->component->getOriginallyMountedActionIndex();

        if (blank($originallyMountedActionIndex)) {
            return false;
        }

        $mountedActionIndex = array_key_last($this->component->mountedActions);

        if (blank($mountedActionIndex)) {
            return false;
        }

        return $originallyMountedActionIndex === $mountedActionIndex;
    }

    public function shouldRenderMountedActionsOnly(bool $whenActionMounted = true): bool
    {
        if (! property_exists($this->component, 'mountedActions')) {
            return false;
        }

        $mountedActionIndex = array_key_last($this->component->mountedActions);

        if ($whenActionMounted && blank($mountedActionIndex)) {
            return false;
        }

        return $this->component->getOriginallyMountedActionIndex() !== $mountedActionIndex;
    }

    public function dehydrate(ComponentContext $context): void
    {
        $partials = [];

        $renderAndQueuePartials = function (Closure $getPartialsUsing) use (&$partials): void {
            foreach ($getPartialsUsing() as $partialName => $view) {
                if (! ($view instanceof View)) {
                    $view = view('filament::anonymous-partial', ['html' => $view]);
                }

                $finish = trigger('render', $this->component, $view, []);

                $revertSharingComponentWithViews = Utils::shareWithViews('__livewire', $this->component);

                $viewContext = app(ViewContext::class);

                $html = $view->render(function (View $view) use ($viewContext): void {
                    $viewContext->extractFromEnvironment($view->getFactory());
                });

                $revertSharingComponentWithViews();

                if (! str_contains($html, "wire:partial=\"{$partialName}\"")) {
                    $html = Utils::insertAttributesIntoHtmlRoot($html, [
                        'wire:partial' => $partialName,
                    ]);
                }

                $replaceHtml = function ($newHtml) use (&$html): void {
                    $html = $newHtml;
                };

                $html = $finish($html, $replaceHtml, $viewContext);

                $partials[$partialName] = $html;
            }
        };

        $isLackingPartialRendersToCoverAllCallsAndUpdates = $this->isLackingPartialRendersToCoverAllCallsAndUpdates();

        if (! $isLackingPartialRendersToCoverAllCallsAndUpdates) {
            $renderAndQueuePartials(function (): array {
                $partials = [];

                foreach ($this->storeGet('partials') ?? [] as $renderPartials) {
                    $partials = [
                        ...$partials,
                        ...$renderPartials(),
                    ];
                }

                return $partials;
            });
        } elseif ($this->shouldRenderMountedActionOnly()) {
            $action = $this->component->getMountedAction();

            $renderAndQueuePartials(fn (): array => [
                "action-modals.{$action->getNestingIndex()}" => $action->renderModal(),
            ]);
        }

        if ($this->shouldRenderMountedActionsOnly(whenActionMounted: $isLackingPartialRendersToCoverAllCallsAndUpdates)) {
            $renderAndQueuePartials(fn (): array => [
                'action-modals' => view('filament-actions::components.modals'),
            ]);
        }

        $context->addEffect('partials', $partials);
    }

    public function skipPartialRender(Component $component): void
    {
        $this->recordPartialRender($component);
    }

    public function renderPartial(Component $component, Closure $renderUsing): void
    {
        store($component)->push('partials', $renderUsing);

        $this->recordPartialRender($component);
    }

    protected function recordPartialRender(Component $component): void
    {
        if (! store($component)->get('isPendingPartialRender')) {
            return;
        }

        store($component)->set('partialRendersCount', (store($component)->get('partialRendersCount') ?? 0) + 1);
        store($component)->set('isPendingPartialRender', false);
    }
}
