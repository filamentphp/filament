<?php

namespace Filament\Tables\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

trait HasActions
{
    /**
     * @deprecated Use the `callMountedAction()` method instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedTableAction(array $arguments = []): mixed
    {
        return $this->callMountedAction($arguments);
    }

    /**
     * @deprecated Use `mountAction()` instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function mountTableAction(string $name, ?string $record = null, array $arguments = []): mixed
    {
        return $this->mountAction($name, $arguments, context: [
            'table' => true,
            'recordKey' => $record,
        ]);
    }

    /**
     * @deprecated Use `mountAction()` instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function replaceMountedTableAction(string $name, ?string $record = null, array $arguments = []): void
    {
        $this->mountAction($name, $arguments, context: [
            'table' => true,
            'recordKey' => $record,
        ]);
    }

    /**
     * @deprecated Use `mountedActionShouldOpenModal()` instead.
     */
    public function mountedTableActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return $this->mountedActionShouldOpenModal($mountedAction);
    }

    /**
     * @deprecated Use `mountedActionHasSchema()` instead.
     */
    public function mountedTableActionHasForm(?Action $mountedAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedAction);
    }

    /**
     * @deprecated Use `getMountedAction()` instead.
     */
    public function getMountedTableAction(): ?Action
    {
        return $this->getMountedAction();
    }

    /**
     * @deprecated Use `getMountedActionSchema()` instead.
     */
    public function getMountedTableActionForm(?Action $mountedAction = null): ?Schema
    {
        return $this->getMountedActionSchema(0, $mountedAction);
    }

    /**
     * @deprecated Use `getMountedAction()?->getRecord()` instead.
     */
    public function getMountedTableActionRecord(): ?Model
    {
        return $this->getMountedAction()?->getRecord();
    }

    /**
     * @deprecated Use `unmountAction()` instead.
     */
    public function unmountTableAction(bool $shouldCancelParentActions = true): void
    {
        $this->unmountAction($shouldCancelParentActions);
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<Action | ActionGroup>
     */
    protected function getTableActions(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableActionsColumnLabel(): ?string
    {
        return null;
    }
}
