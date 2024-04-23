<?php

namespace Filament\Support\Partials;

use Closure;
use Livewire\Component;
use Livewire\ComponentHook;
use Livewire\Features\SupportValidation\SupportValidation;
use Livewire\Mechanisms\ExtendBlade\ExtendBlade;
use Livewire\Mechanisms\HandleComponents\ComponentContext;

use function Livewire\store;

class SupportPartials extends ComponentHook
{
    public function hydrate(): void
    {
        if (! app()->runningUnitTests()) {
            $this->storeSet('skipRender', function (): bool {
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
            });
        }
    }

    public function call(): Closure
    {
        $this->storeSet('callsCount', ($this->storeGet('callsCount') ?? 0) + 1);

        $this->storeSet('isPendingPartialRender', true);

        return function () {
            if (! $this->storeGet('skipRender')) {
                return;
            }

            $this->skipPartialRender($this->component);
        };
    }

    public function update(): Closure
    {
        $this->storeSet('updatesCount', ($this->storeGet('updatesCount') ?? 0) + 1);

        $this->storeSet('isPendingPartialRender', true);

        return function () {
            if (! $this->storeGet('skipRender')) {
                return;
            }

            $this->skipPartialRender($this->component);
        };
    }

    public function isLackingPartialRendersToCoverAllCallsAndUpdates(): bool
    {
        $effects = intval($this->storeGet('callsCount') ?? 0) + intval($this->storeGet('updatesCount') ?? 0);

        if (! $effects) {
            return true;
        }

        $renders = count($this->storeGet('partials') ?? []) + intval($this->storeGet('partialSkipsCount') ?? 0);

        if (! $renders) {
            return true;
        }

        return $effects > $renders;
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
            app(ExtendBlade::class)->startLivewireRendering($this->component);

            $supportValidationHook = app(SupportValidation::class);
            $supportValidationHook->setComponent($this->component);

            $revertSupportValidation = $supportValidationHook->render(null, null);

            $partials = [
                ...$partials,
                ...$getPartialsUsing(),
            ];

            $revertSupportValidation();

            app(ExtendBlade::class)->endLivewireRendering();
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
            $actionNestingIndex = array_key_last($this->component->mountedActions);

            $renderAndQueuePartials(fn (): array => [
                "action-modals.{$actionNestingIndex}" => $this->component->getMountedAction()->renderModal($actionNestingIndex)->toHtml(),
            ]);
        }

        if ($this->shouldRenderMountedActionsOnly(whenActionMounted: $isLackingPartialRendersToCoverAllCallsAndUpdates)) {
            $renderAndQueuePartials(fn (): array => [
                'action-modals' => view('filament-actions::components.modals')->render(),
            ]);
        }

        $context->addEffect('partials', $partials);
    }

    public function skipPartialRender(Component $component): void
    {
        if (! store($component)->get('isPendingPartialRender')) {
            return;
        }

        store($component)->set('partialSkipsCount', (store($component)->get('partialSkipsCount') ?? 0) + 1);

        store($component)->set('isPendingPartialRender', false);
    }

    public function renderPartial(Component $component, Closure $renderUsing): void
    {
        if (! store($component)->get('isPendingPartialRender')) {
            return;
        }

        store($component)->push('partials', $renderUsing);

        store($component)->set('isPendingPartialRender', false);
    }
}
