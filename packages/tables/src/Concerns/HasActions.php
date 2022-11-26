<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Scalar;

/**
 * @property Form $mountedTableActionForm
 */
trait HasActions
{
    /**
     * @var string $mountedTableAction
     */
    public $mountedTableAction = null;

    /**
     * @var array<string, mixed> $mountedTableActionData
     */
    public $mountedTableActionData = [];

    /**
     * @var scalar $mountedTableActionRecord
     */
    public $mountedTableActionRecord = null;

    protected ?Model $cachedMountedTableActionRecord = null;

    /**
     * @var scalar $cachedMountedTableActionRecordKey
     */
    protected $cachedMountedTableActionRecordKey = null;

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
            return;
        } catch (Cancel $exception) {
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

    /**
     * @param scalar $record
     */
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
            return;
        } catch (Cancel $exception) {
            $this->mountedTableAction = null;
            $this->mountedTableActionRecord(null);

            return;
        }

        if (! $this->mountedTableActionShouldOpenModal()) {
            return $this->callMountedTableAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-table-action",
        ]);
    }

    public function mountedTableActionShouldOpenModal(): bool
    {
        $action = $this->getMountedTableAction();

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
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
                ->context($this->mountedTableAction),
        );
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

    /**
     * @deprecated Override the `table()` method to configure the table.
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
