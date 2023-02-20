<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ComponentContainer $mountedTableActionForm
 */
trait HasActions
{
    public $mountedTableAction = null;

    public $mountedTableActionData = [];

    public $mountedTableActionRecord = null;

    protected array $cachedTableActions;

    protected array $cachedTableColumnActions;

    protected ?Model $cachedMountedTableActionRecord = null;

    protected $cachedMountedTableActionRecordKey = null;

    public function cacheTableActions(): void
    {
        $this->cachedTableActions = [];

        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureTableAction']),
            fn (): array => $this->getTableActions(),
        );

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->table($this->getCachedTable());
                }

                $this->cachedTableActions[$index] = $action;

                continue;
            }

            $action->table($this->getCachedTable());

            $this->cachedTableActions[$action->getName()] = $action;
        }
    }

    public function cacheTableColumnActions(): void
    {
        $this->cachedTableColumnActions = [];

        foreach ($this->getCachedTableColumns() as $column) {
            $action = $column->getAction();

            if (! ($action instanceof Action)) {
                continue;
            }

            $actionName = $action->getName();

            if (array_key_exists($actionName, $this->cachedTableColumnActions)) {
                continue;
            }

            $action->table($this->getCachedTable());

            $this->cachedTableColumnActions[$actionName] = $action;
        }
    }

    protected function configureTableAction(Action $action): void
    {
    }

    public function callMountedTableAction(?string $arguments = null)
    {
        $action = $this->getMountedTableAction();

        if (! $action) {
            return;
        }

        if (filled($this->mountedTableActionRecord) && ($action->getRecord() === null)) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

        $form = $this->getMountedTableActionForm();

        $result = null;

        try {
            if ($action->hasForm()) {
                $action->callBeforeFormValidated();

                $action->formData($form->getState());

                $action->callAfterFormValidated();
            }

            $action->callBefore();

            $result = $action->call([
                'form' => $form,
            ]);

            $result = $action->callAfter() ?? $result;
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
        }

        if (filled($this->redirectTo)) {
            return $result;
        }

        $this->mountedTableAction = null;

        $action->record(null);
        $this->mountedTableActionRecord(null);

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => "{$this->id}-table-action",
        ]);

        return $result;
    }

    public function mountedTableActionRecord($record): void
    {
        $this->mountedTableActionRecord = $record;
    }

    public function mountTableAction(string $name, ?string $record = null)
    {
        $this->mountedTableAction = $name;
        $this->mountedTableActionRecord($record);

        $action = $this->getMountedTableAction();

        if (! $action) {
            return;
        }

        if (filled($record) && ($action->getRecord() === null)) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $this->cacheForm(
            'mountedTableActionForm',
            fn () => $this->getMountedTableActionForm(),
        );

        try {
            if ($action->hasForm()) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableActionForm(),
            ]);

            if ($action->hasForm()) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
            $this->mountedTableAction = null;
            $this->mountedTableActionRecord(null);

            return;
        }

        if (! $action->shouldOpenModal()) {
            return $this->callMountedTableAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-table-action",
        ]);
    }

    public function getCachedTableActions(): array
    {
        return $this->cachedTableActions;
    }

    public function getCachedTableColumnActions(): array
    {
        return $this->cachedTableColumnActions;
    }

    public function getMountedTableAction(): ?Action
    {
        if (! $this->mountedTableAction) {
            return null;
        }

        return $this->getCachedTableAction($this->mountedTableAction) ?? $this->getCachedTableEmptyStateAction($this->mountedTableAction) ?? $this->getCachedTableHeaderAction($this->mountedTableAction);
    }

    public function getMountedTableActionForm(): ?ComponentContainer
    {
        $action = $this->getMountedTableAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableActionForm')) {
            return $this->getCachedForm('mountedTableActionForm');
        }

        return $this->makeForm()
            ->schema($action->getFormSchema())
            ->model($this->getMountedTableActionRecord() ?? $this->getTableQuery()->getModel()::class)
            ->statePath('mountedTableActionData')
            ->context($this->mountedTableAction);
    }

    public function getMountedTableActionRecordKey()
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

    public function getCachedTableAction(string $name): ?Action
    {
        return $this->findTableAction($name)?->record($this->getMountedTableActionRecord());
    }

    protected function findTableAction(string $name): ?Action
    {
        $actions = $this->getCachedTableActions();

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

        $actions = $this->getCachedTableColumnActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action;
        }

        return null;
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableActionsPosition(): ?string
    {
        return null;
    }

    protected function getTableActionsColumnLabel(): ?string
    {
        return null;
    }
}
