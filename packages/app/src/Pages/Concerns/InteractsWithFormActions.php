<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use InvalidArgumentException;

trait InteractsWithFormActions
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $cachedFormActions = [];

    public function bootedInteractsWithFormActions(): void
    {
        $this->cacheFormActions();
    }

    protected function cacheFormActions(): void
    {
        /** @var array<string, Action | ActionGroup> */
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getFormActions(),
        );

        foreach ($actions as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);
                $this->cachedFormActions[] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Form actions must be an instance of ' . Action::class . ', or ' . ActionGroup::class . '.');
            }

            $this->cacheAction($action);
            $this->cachedFormActions[] = $action;
        }
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getCachedFormActions(): array
    {
        return $this->cachedFormActions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        return [];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
