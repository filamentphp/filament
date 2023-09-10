<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;

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
     * @var int | string | null
     */
    public $mountedTableActionRecord = null;

    protected ?Model $cachedMountedTableActionRecord = null;

    protected int | string | null $cachedMountedTableActionRecordKey = null;

    protected function configureTableAction(Action $action): void
    {
    }

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

        $action->arguments($arguments);

        $form = $this->getMountedTableActionForm();

        $result = null;

        try {
            if ($this->mountedTableActionHasForm()) {
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
            return null;
        } catch (Cancel $exception) {
        }

        $action->resetArguments();
        $action->resetFormData();

        if (store($this)->has('redirect')) {
            return $result;
        }

        $this->unmountTableAction();

        return $result;
    }

    public function mountedTableActionRecord(int | string | null $record): void
    {
        $this->mountedTableActionRecord = $record;
    }

    public function mountTableAction(string $name, ?string $record = null): mixed
    {
        $this->mountedTableActions[] = $name;
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

        $this->cacheMountedTableActionForm();

        try {
            $hasForm = $this->mountedTableActionHasForm();

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableActionForm(),
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

        if (! $this->mountedTableActionShouldOpenModal()) {
            return $this->callMountedTableAction();
        }

        $this->resetErrorBag();

        $this->openTableActionModal();

        return null;
    }

    public function mountedTableActionShouldOpenModal(): bool
    {
        $action = $this->getMountedTableAction();

        if ($action->isModalHidden()) {
            return false;
        }

        return $action->getModalDescription() ||
            $action->getModalContent() ||
            $action->getModalContentFooter() ||
            $action->getInfolist() ||
            $this->mountedTableActionHasForm();
    }

    public function mountedTableActionHasForm(): bool
    {
        return (bool) count($this->getMountedTableActionForm()?->getComponents() ?? []);
    }

    public function getMountedTableAction(): ?Action
    {
        if (! count($this->mountedTableActions ?? [])) {
            return null;
        }

        return $this->getTable()->getAction($this->mountedTableActions);
    }

    public function getMountedTableActionForm(): ?Form
    {
        $action = $this->getMountedTableAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableActionForm')) {
            return $this->getForm('mountedTableActionForm');
        }

        return $action->getForm(
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
            array_pop($this->mountedTableActionsData);
        }
    }

    protected function resetMountedTableActionProperties(): void
    {
        $this->mountedTableActions = [];
        $this->mountedTableActionsData = [];
    }

    public function unmountTableAction(bool $shouldCancelParentActions = true): void
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
            $this->closeTableActionModal();

            $action?->record(null);
            $this->mountedTableActionRecord(null);

            return;
        }

        $this->cacheMountedTableActionForm();

        $this->resetErrorBag();

        $this->openTableActionModal();
    }

    protected function cacheMountedTableActionForm(): void
    {
        $this->cacheForm(
            'mountedTableActionForm',
            fn () => $this->getMountedTableActionForm(),
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
