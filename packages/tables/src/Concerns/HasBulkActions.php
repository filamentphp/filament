<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\BulkActions\BulkAction;

trait HasBulkActions
{
    protected array $cachedTableBulkActions;

    public function cacheTableBulkActions(): void
    {
        $this->cachedTableBulkActions = collect($this->getTableBulkActions())
            ->mapWithKeys(function (BulkAction $action): array {
                $action->table($this->getTable());

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function runTableBulkAction(string $name): void
    {
        $action = $this->getCachedTableBulkAction($name);

        if (! $action) {
            return;
        }

        $action->run($this->getSelectedTableRecords());
    }

    public function getCachedTableBulkActions(): array
    {
        return $this->cachedTableBulkActions;
    }

    public function getTableBulkActions(): array
    {
        return [];
    }

    protected function getCachedTableBulkAction(string $name): ?BulkAction
    {
        return $this->getCachedTableBulkActions()[$name] ?? null;
    }
}
