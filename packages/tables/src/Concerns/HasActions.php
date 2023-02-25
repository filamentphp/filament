<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Form $mountedTableActionForm
 */
trait HasActions
{
    public ?string $mountedTableAction = null;

    /**
     * @var array<string, mixed>
     */
    public $mountedTableActionData = [];

    public int | string | null $mountedTableActionRecord = null;

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

    public function mountedTableActionRecord(int | string | null $record): void
    {
        $this->mountedTableActionRecord = $record;
    }

    public function mountTableAction(string $name, ?string $record = null): mixed
    {
        $this->mountedTableAction = $name;
        $this->mountedTableActionRecord($record);

        $action = $this->getMountedTableAction();

        if (! $action) {
            return null;
        }

        if (filled($record) && ($action->getRecord() === null)) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $this->cacheForm(
            'mountedTableActionForm',
            fn () => $this->getMountedTableActionForm(),
        );

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
            $this->mountedTableAction = null;
            $this->mountedTableActionRecord(null);

            return null;
        }

        if (! $this->mountedTableActionShouldOpenModal()) {
            return $this->callMountedTableAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-table-action",
        ]);

        return null;
    }

    public function mountedTableActionShouldOpenModal(): bool
    {
        $action = $this->getMountedTableAction();

        if ($action->isModalHidden()) {
            return false;
        }

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
            $action->getInfolist() ||
            $this->mountedTableActionHasForm();
    }

    public function mountedTableActionHasForm(): bool
    {
        return (bool) count($this->getMountedTableActionForm()?->getComponents() ?? []);
    }

    public function getMountedTableAction(): ?Action
    {
        if (! $this->mountedTableAction) {
            return null;
        }

        return $this->getTable()->getAction($this->mountedTableAction) ?? $this->getTable()->getEmptyStateAction($this->mountedTableAction) ?? $this->getTable()->getHeaderAction($this->mountedTableAction);
    }

    public function getMountedTableActionForm(): ?Form
    {
        $action = $this->getMountedTableAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableActionForm')) {
            return $this->getCachedForm('mountedTableActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->model($this->getMountedTableActionRecord() ?? $this->getTable()->getModel())
                ->statePath('mountedTableActionData')
                ->operation($this->mountedTableAction),
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
    protected function getTableActionsPosition(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableActionsColumnLabel(): ?string
    {
        return null;
    }
}
