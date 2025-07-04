<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Throwable;

use function Livewire\store;

/**
 * @property Form $mountedTableActionForm
 */
trait HasActions
{
    /**
     * @var array<string> | null
     */
    public ?array $mountedTableActions = [];

    /**
     * @var array<string, array<string, mixed>> | null
     */
    public ?array $mountedTableActionsData = [];

    /**
     * @var array<string, array<string, mixed>> | null
     */
    public ?array $mountedTableActionsArguments = [];

    /**
     * @var int | string | null
     */
    public $mountedTableActionRecord = null;

    protected ?Model $cachedMountedTableActionRecord = null;

    protected int | string | null $cachedMountedTableActionRecordKey = null;

    /**
     * @var mixed
     */
    #[Url(as: 'tableAction')]
    public $defaultTableAction = null;

    /**
     * @var mixed
     */
    #[Url(as: 'tableActionArguments')]
    public $defaultTableActionArguments = null;

    /**
     * @var mixed
     */
    #[Url(as: 'tableActionRecord')]
    public $defaultTableActionRecord = null;

    protected function configureTableAction(Action $action): void {}

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedTableAction(array $arguments = []): mixed
    {
        $action = $this->getMountedTableAction();

        if (! $action) {
            return null;
        }

        if (filled($this->mountedTableActionRecord) && ($action->getRecord() === null)) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->mergeArguments($arguments);

        $form = $this->getMountedTableActionForm(mountedAction: $action);

        $result = null;

        $originallyMountedActions = $this->mountedTableActions;

        try {
            $action->beginDatabaseTransaction();

            if ($this->mountedTableActionHasForm(mountedAction: $action)) {
                $action->callBeforeFormValidated();

                $form->getState(afterValidate: function (array $state) use ($action) {
                    $action->callAfterFormValidated();

                    $action->formData($state);

                    $action->callBefore();
                });
            } else {
                $action->callBefore();
            }

            $result = $action->call([
                'form' => $form,
            ]);

            $result = $action->callAfter() ?? $result;
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();

            return null;
        } catch (Cancel $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();
        } catch (ValidationException $exception) {
            $action->rollBackDatabaseTransaction();

            if (! $this->mountedTableActionShouldOpenModal(mountedAction: $action)) {
                $action->resetArguments();
                $action->resetFormData();

                $this->unmountTableAction();
            }

            throw $exception;
        } catch (Throwable $exception) {
            $action->rollBackDatabaseTransaction();

            throw $exception;
        }

        $action->commitDatabaseTransaction();

        if (store($this)->has('redirect')) {
            return $result;
        }

        $action->resetArguments();
        $action->resetFormData();

        // If the action was replaced while it was being called,
        // we don't want to unmount it.
        if ($originallyMountedActions !== $this->mountedTableActions) {
            return null;
        }

        $this->unmountTableAction();

        return $result;
    }

    public function mountedTableActionRecord(int | string | null $record): void
    {
        $this->mountedTableActionRecord = $record;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mountTableAction(string $name, ?string $record = null, array $arguments = []): mixed
    {
        $this->mountedTableActions[] = $name;
        $this->mountedTableActionsArguments[] = $arguments;
        $this->mountedTableActionsData[] = [];

        if (count($this->mountedTableActions) === 1) {
            $this->mountedTableActionRecord($record);
        }

        $action = $this->getMountedTableAction();

        if (! $action) {
            $this->unmountTableAction();

            return null;
        }

        if (filled($record) && ($action->getRecord() === null)) {
            $this->unmountTableAction();

            return null;
        }

        if ($action->isDisabled()) {
            $this->unmountTableAction();

            return null;
        }

        $this->cacheMountedTableActionForm(mountedAction: $action);

        try {
            $hasForm = $this->mountedTableActionHasForm(mountedAction: $action);

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableActionForm(mountedAction: $action),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->unmountTableAction(shouldCancelParentActions: false);

            return null;
        }

        if (! $this->mountedTableActionShouldOpenModal(mountedAction: $action)) {
            return $this->callMountedTableAction();
        }

        $this->resetErrorBag();

        $this->openTableActionModal();

        return null;
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function replaceMountedTableAction(string $name, ?string $record = null, array $arguments = []): void
    {
        $this->resetMountedTableActionProperties();
        $this->mountTableAction($name, $record ?? $this->mountedTableActionRecord, $arguments);
    }

    public function mountedTableActionShouldOpenModal(?Action $mountedAction = null): bool
    {
        return ($mountedAction ?? $this->getMountedTableAction())->shouldOpenModal(
            checkForFormUsing: $this->mountedTableActionHasForm(...),
        );
    }

    public function mountedTableActionHasForm(?Action $mountedAction = null): bool
    {
        return (bool) count($this->getMountedTableActionForm(mountedAction: $mountedAction)?->getComponents() ?? []);
    }

    public function getMountedTableAction(): ?Action
    {
        if (! count($this->mountedTableActions ?? [])) {
            return null;
        }

        return $this->getTable()->getAction($this->mountedTableActions);
    }

    public function getMountedTableActionForm(?Action $mountedAction = null): ?Form
    {
        $mountedAction ??= $this->getMountedTableAction();

        if (! $mountedAction) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableActionForm')) {
            return $this->getForm('mountedTableActionForm');
        }

        return $mountedAction->getForm(
            $this->makeForm()
                ->model($this->getMountedTableActionRecord() ?? $this->getTable()->getModel())
                ->statePath('mountedTableActionsData.' . array_key_last($this->mountedTableActionsData))
                ->operation(implode('.', $this->mountedTableActions)),
        );
    }

    public function getMountedTableActionRecordKey(): int | string | null
    {
        return $this->mountedTableActionRecord;
    }

    public function getMountedTableActionRecord(): ?Model
    {
        $recordKey = $this->getMountedTableActionRecordKey();

        if ($this->cachedMountedTableActionRecord && ($this->cachedMountedTableActionRecordKey === $recordKey)) {
            return $this->cachedMountedTableActionRecord;
        }

        $this->cachedMountedTableActionRecordKey = $recordKey;

        return $this->cachedMountedTableActionRecord = $this->getTableRecord($recordKey);
    }

    protected function popMountedTableAction(): ?string
    {
        try {
            return array_pop($this->mountedTableActions);
        } finally {
            array_pop($this->mountedTableActionsArguments);
            array_pop($this->mountedTableActionsData);
        }
    }

    protected function resetMountedTableActionProperties(): void
    {
        $this->mountedTableActions = [];
        $this->mountedTableActionsArguments = [];
        $this->mountedTableActionsData = [];
    }

    public function unmountTableAction(bool $shouldCancelParentActions = true, bool $shouldCloseModal = true): void
    {
        $action = $this->getMountedTableAction();

        if (! ($shouldCancelParentActions && $action)) {
            $this->popMountedTableAction();
        } elseif ($action->shouldCancelAllParentActions()) {
            $this->resetMountedTableActionProperties();
        } else {
            $parentActionToCancelTo = $action->getParentActionToCancelTo();

            while (true) {
                $recentlyClosedParentAction = $this->popMountedTableAction();

                if (
                    blank($parentActionToCancelTo) ||
                    ($recentlyClosedParentAction === $parentActionToCancelTo)
                ) {
                    break;
                }
            }
        }

        if (! count($this->mountedTableActions)) {
            if ($shouldCloseModal) {
                $this->closeTableActionModal();
            }

            $action?->record(null);
            $this->mountedTableActionRecord(null);

            // Setting these to `null` creates a bug where the properties are
            // actually set to `'null'` strings and remain in the URL.
            $this->defaultTableAction = [];
            $this->defaultTableActionArguments = [];
            $this->defaultTableActionRecord = [];

            $this->selectedTableRecords = [];

            return;
        }

        $this->cacheMountedTableActionForm();

        $this->resetErrorBag();

        $this->openTableActionModal();
    }

    protected function cacheMountedTableActionForm(?Action $mountedAction = null): void
    {
        $this->cacheForm(
            'mountedTableActionForm',
            fn () => $this->getMountedTableActionForm($mountedAction),
        );
    }

    protected function closeTableActionModal(): void
    {
        $this->dispatch('close-modal', id: "{$this->getId()}-table-action");
    }

    protected function openTableActionModal(): void
    {
        $this->dispatch('open-modal', id: "{$this->getId()}-table-action");
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

    public function mountedTableActionInfolist(): Infolist
    {
        return $this->getMountedTableAction()->getInfolist();
    }
}
