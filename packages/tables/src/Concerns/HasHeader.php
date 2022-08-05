<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Contracts\View\View;

trait HasHeader
{
    protected array $cachedTableHeaderActions;

    public function cacheTableHeaderActions(): void
    {
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureTableAction']),
            fn (): array => $this->getTableHeaderActions(),
        );

        $this->cachedTableHeaderActions = [];

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->table($this->getCachedTable());
                }

                $this->cachedTableHeaderActions[$index] = $action;

                continue;
            }

            $action->table($this->getCachedTable());

            $this->cachedTableHeaderActions[$action->getName()] = $action;
        }
    }

    public function getCachedTableHeaderActions(): array
    {
        return $this->cachedTableHeaderActions;
    }

    public function getCachedTableHeaderAction(string $name): ?Action
    {
        $actions = $this->getCachedTableHeaderActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action;
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $groupedAction;
        }

        return null;
    }

    protected function getTableDescription(): ?string
    {
        return null;
    }

    protected function getTableHeader(): ?View
    {
        return null;
    }

    protected function getTableHeaderActions(): array
    {
        return [];
    }

    protected function getTableHeading(): string | Closure | null
    {
        return null;
    }
}
