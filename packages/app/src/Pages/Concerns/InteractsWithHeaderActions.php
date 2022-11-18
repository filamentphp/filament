<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait InteractsWithHeaderActions
{
    protected array $cachedHeaderActions = [];

    public function bootedInteractsWithHeaderActions(): void
    {
        $this->cacheHeaderActions();
    }

    protected function cacheHeaderActions(): void
    {
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getHeaderActions(),
        );

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->livewire($this);

                    $this->cacheAction($groupedAction);
                }

                $this->cachedHeaderActions[$index] = $action;

                continue;
            }

            $this->cacheAction($action);
            $this->cachedHeaderActions[$action->getName()] = $action;
        }
    }

    public function getCachedHeaderActions(): array
    {
        return $this->cachedHeaderActions;
    }

    protected function getHeaderActions(): array
    {
        return $this->getActions();
    }

    /**
     * @deprecated Register header actions within the `getHeaderActions()` method instead.
     */
    protected function getActions(): array
    {
        return [];
    }
}
