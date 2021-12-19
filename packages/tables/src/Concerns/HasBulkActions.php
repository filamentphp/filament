<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\BulkAction;

trait HasBulkActions
{
    public $mountedTableBulkAction = null;

    public $mountedTableBulkActionData = [];

    protected array $cachedTableBulkActions;

    public function cacheTableBulkActions(): void
    {
        $this->cachedTableBulkActions = collect($this->getTableBulkActions())
            ->filter(fn (BulkAction $action): bool => ! $action->isHidden())
            ->mapWithKeys(function (BulkAction $action): array {
                $action->table($this->getCachedTable());

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function callMountedTableBulkAction()
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        $data = $this->getMountedTableBulkActionForm()->getState();

        try {
            return $action->records($this->getSelectedTableRecords())->call($data);
        } finally {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => static::class . '-bulk-action',
            ]);
        }
    }

    public function mountTableBulkAction(string $name)
    {
        $this->mountedTableBulkAction = $name;

        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        $this->cacheForm('mountedTableBulkActionForm');

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedTableBulkActionForm(),
            'records' => $this->getSelectedTableRecords(),
        ]);

        if (! $action->shouldOpenModal()) {
            return $this->callMountedTableBulkAction();
        }

        $this->dispatchBrowserEvent('open-modal', [
            'id' => static::class . '-bulk-action',
        ]);
    }

    public function getCachedTableBulkActions(): array
    {
        return $this->cachedTableBulkActions;
    }

    public function getMountedTableBulkAction(): ?BulkAction
    {
        if (! $this->mountedTableBulkAction) {
            return null;
        }

        return $this->getCachedTableBulkAction($this->mountedTableBulkAction);
    }

    public function getMountedTableBulkActionForm(): ComponentContainer
    {
        return $this->mountedTableBulkActionForm;
    }

    protected function getCachedTableBulkAction(string $name): ?BulkAction
    {
        return $this->getCachedTableBulkActions()[$name] ?? null;
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
}
