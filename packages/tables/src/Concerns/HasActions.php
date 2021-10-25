<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Actions\Action;

trait HasActions
{
    protected array $cachedTableActions;

    public function cacheTableActions(): void
    {
        $this->cachedTableActions = collect($this->getTableActions())
            ->mapWithKeys(function (Action $action): array {
                $action->table($this->getTable());

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function callTableAction(string $name, string $recordKey): void
    {
        $record = $this->resolveTableRecord($recordKey);

        if (! $record) {
            return;
        }

        $action = $this->getCachedTableAction($name);

        if (! $action) {
            return;
        }

        $action->record($record)->callAction();
    }

    public function getCachedTableActions(): array
    {
        return $this->cachedTableActions;
    }

    public function getTableActions(): array
    {
        return [];
    }

    protected function getCachedTableAction(string $name): ?Action
    {
        return $this->getCachedTableActions()[$name] ?? null;
    }
}
