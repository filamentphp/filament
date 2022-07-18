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
            fn (Action $action) => $this->configureTableAction($action->button()),
            fn (): array => $this->getTableHeaderActions(),
        );

        $this->cachedTableHeaderActions = collect($actions)
            ->mapWithKeys(function (Action | ActionGroup $action): array {
                $action->table($this->getCachedTable());

                return [$action instanceof Action ? $action->getName() : null => $action];
            })
            ->toArray();
    }

    public function getCachedTableHeaderActions(): array
    {
        return collect($this->cachedTableHeaderActions)
            ->filter(fn (Action | ActionGroup $action): bool => ! $action->isHidden())
            ->toArray();
    }

    public function getCachedTableHeaderAction(string $name): ?Action
    {
        return $this->getCachedTableHeaderActions()[$name] ?? null;
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
