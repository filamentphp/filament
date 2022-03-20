<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\BulkAction;

/**
 * @property ComponentContainer $mountedTableBulkActionForm
 */
trait HasBulkActions
{
    public $mountedTableBulkAction = null;

    public $mountedTableBulkActionData = [];

    protected array $cachedTableBulkActions;

    public function cacheTableBulkActions(): void
    {
        $this->cachedTableBulkActions = collect($this->getTableBulkActions())
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

        if ($action->isHidden()) {
            return;
        }

        $data = $this->getMountedTableBulkActionForm()->getState();

        try {
            return $action->call($data);
        } finally {
            $this->selectedTableRecords = [];

            $this->dispatchBrowserEvent('close-modal', [
                'id' => static::class . '-bulk-action',
            ]);
        }
    }

    public function mountTableBulkAction(string $name, array $selectedRecords)
    {
        $this->mountedTableBulkAction = $name;
        $this->selectedTableRecords = $selectedRecords;

        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        if ($action->isHidden()) {
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

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => static::class . '-bulk-action',
        ]);
    }

    public function getCachedTableBulkActions(): array
    {
        return collect($this->cachedTableBulkActions)
            ->filter(fn (BulkAction $action): bool => ! $action->isHidden())
            ->toArray();
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
        $action = $this->getCachedTableBulkActions()[$name] ?? null;
        $action?->records($this->getSelectedTableRecords());

        return $action;
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
}
