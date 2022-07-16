<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;

trait HasEmptyState
{
    protected array $cachedTableEmptyStateActions;

    public function cacheTableEmptyStateActions(): void
    {
        Action::configureUsing(
            fn (Action $action): Action => $action->button(),
            function (): void {
                $this->cachedTableEmptyStateActions = collect($this->getTableEmptyStateActions())
                    ->mapWithKeys(function (Action $action): array {
                        $action->table($this->getCachedTable());

                        return [$action->getName() => $action];
                    })
                    ->toArray();
            }
        );
    }

    public function getCachedTableEmptyStateActions(): array
    {
        return collect($this->cachedTableEmptyStateActions)
            ->filter(fn (Action $action): bool => ! $action->isHidden())
            ->toArray();
    }

    public function getCachedTableEmptyStateAction(string $name): ?Action
    {
        return $this->getCachedTableEmptyStateActions()[$name] ?? null;
    }

    protected function getTableEmptyState(): ?View
    {
        return null;
    }

    protected function getTableEmptyStateActions(): array
    {
        return [];
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return null;
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return null;
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return null;
    }
}
