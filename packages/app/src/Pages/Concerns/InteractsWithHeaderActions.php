<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use InvalidArgumentException;

trait InteractsWithHeaderActions
{
    /**
     * @var array<string, Action | ActionGroup>
     */
    protected array $cachedHeaderActions = [];

    public function bootedInteractsWithHeaderActions(): void
    {
        $this->cacheHeaderActions();
    }

    protected function cacheHeaderActions(): void
    {
        /** @var array<string, Action | ActionGroup> */
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getHeaderActions(),
        );

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    if (! $groupedAction instanceof Action) {
                        throw new InvalidArgumentException('Header actions within a group must be an instance of ' . Action::class . '.');
                    }

                    /** @var Action $groupedAction */
                    $groupedAction->livewire($this);

                    $this->cacheAction($groupedAction);
                }

                $this->cachedHeaderActions[$index] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Header actions must be an instance of ' . Action::class . ', or ' . ActionGroup::class . '.');
            }

            $this->cacheAction($action);
            $this->cachedHeaderActions[$action->getName()] = $action;
        }
    }

    /**
     * @return array<string, Action | ActionGroup>
     */
    public function getCachedHeaderActions(): array
    {
        return $this->cachedHeaderActions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return $this->getActions();
    }

    /**
     * @deprecated Register header actions within the `getHeaderActions()` method instead.
     *
     * @return array<Action | ActionGroup>
     */
    protected function getActions(): array
    {
        return [];
    }
}
