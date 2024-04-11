<?php

namespace Filament\Support\Partials;

use Closure;
use Livewire\Component;
use Livewire\ComponentHook;
use Livewire\Mechanisms\ExtendBlade\ExtendBlade;
use Livewire\Mechanisms\HandleComponents\ComponentContext;

use function Livewire\store;

class SupportPartials extends ComponentHook
{
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

    public function shouldSkipRender(): bool
    {
        if (! $this->shouldRender()) {
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

    public function shouldRender(): bool
    {
        $effects = ($this->storeGet('callsCount') ?? 0) + ($this->storeGet('updatesCount') ?? 0);

        if (! $effects) {
            return true;
        }

        $renders = count($this->storeGet('partials') ?? []) + ($this->storeGet('partialSkipsCount') ?? 0);

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

        if (blank($this->component->mountedActions)) {
            return false;
        }

        return $originallyMountedActionIndex === array_key_last($this->component->mountedActions);
    }

    public function shouldRenderMountedActionsOnly(): bool
    {
        if (! property_exists($this->component, 'mountedActions')) {
            return false;
        }

        return $this->component->getOriginallyMountedActionIndex() !== array_key_last($this->component->mountedActions);
    }

    public function dehydrate(ComponentContext $context): void
    {
        if (! $this->shouldRender()) {
            $replacements = [];

            app(ExtendBlade::class)->startLivewireRendering($this->component);

            foreach ($this->storeGet('partials') ?? [] as $renderPartial) {
                $replacements = [
                    ...$replacements,
                    ...$renderPartial(),
                ];
            }

            app(ExtendBlade::class)->endLivewireRendering();

            $context->addEffect('partials', $replacements);

            return;
        }

        if ($this->shouldRenderMountedActionOnly()) {
            $actionNestingIndex = array_key_last($this->component->mountedActions);

            app(ExtendBlade::class)->startLivewireRendering($this->component);

            $context->addEffect('partials', [
                "action-modals.{$actionNestingIndex}" => $this->component->getMountedAction()->renderModal($actionNestingIndex)->toHtml(),
            ]);

            app(ExtendBlade::class)->endLivewireRendering();
        }

        if ($this->shouldRenderMountedActionsOnly()) {
            app(ExtendBlade::class)->startLivewireRendering($this->component);

            $context->addEffect('partials', [
                'action-modals' => view('filament-actions::components.modals')->render(),
            ]);

            app(ExtendBlade::class)->endLivewireRendering();
        }
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
