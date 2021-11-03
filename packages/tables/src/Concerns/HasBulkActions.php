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
            return $action->call($this->getSelectedTableRecords(), $data);
        } finally {
            $this->dispatchBrowserEvent('close-modal', [
                'id' => 'bulk-action',
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

        if (! $action->shouldOpenModal()) {
            return $this->callMountedTableBulkAction();
        }

        $this->getMountedTableBulkActionForm()->fill();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'bulk-action',
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
        return $this->mountedTableBulkActionForm
            ->schema($this->getMountedTableBulkAction()->getFormSchema())
            ->model($this->getTableQuery()->getModel()::class);
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
