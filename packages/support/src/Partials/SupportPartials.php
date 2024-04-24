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

    public function update(...$args): void
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
