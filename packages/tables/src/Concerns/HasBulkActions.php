<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\BulkAction;

/**
 * @property Form $mountedTableBulkActionForm
 */
trait HasBulkActions
{
    public ?string $mountedTableBulkAction = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $mountedTableBulkActionData = [];

    protected function configureTableBulkAction(BulkAction $action): void
    {
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedTableBulkAction(array $arguments = []): mixed
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->arguments($arguments);

        $form = $this->getMountedTableBulkActionForm();

        $result = null;

        try {
            if ($this->mountedTableBulkActionHasForm()) {
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

        $this->mountedTableBulkAction = null;
        $this->selectedTableRecords = [];

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => "{$this->id}-table-bulk-action",
        ]);

        return $result;
    }

    /**
     * @param  array<int | string>  $selectedRecords
     */
    public function mountTableBulkAction(string $name, array $selectedRecords): mixed
    {
        $this->mountedTableBulkAction = $name;
        $this->selectedTableRecords = $selectedRecords;

        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $this->cacheForm(
            'mountedTableBulkActionForm',
            fn () => $this->getMountedTableBulkActionForm(),
        );

        try {
            $hasForm = $this->mountedTableBulkActionHasForm();

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableBulkActionForm(),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->mountedTableBulkAction = null;
            $this->selectedTableRecords = [];

            return null;
        }

        if (! $this->mountedTableBulkActionShouldOpenModal()) {
            return $this->callMountedTableBulkAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-table-bulk-action",
        ]);

        return null;
    }

    public function mountedTableBulkActionShouldOpenModal(): bool
    {
        $action = $this->getMountedTableBulkAction();

        if ($action->isModalHidden()) {
            return false;
        }

        return $action->getModalSubheading() ||
            $action->getModalContent() ||
            $action->getModalFooter() ||
            $action->getInfolist() ||
            $this->mountedTableBulkActionHasForm();
    }

    public function mountedTableBulkActionHasForm(): bool
    {
        return (bool) count($this->getMountedTableBulkActionForm()?->getComponents() ?? []);
    }

    public function getMountedTableBulkAction(): ?BulkAction
    {
        if (! $this->mountedTableBulkAction) {
            return null;
        }

        return $this->getTable()->getBulkAction($this->mountedTableBulkAction);
    }

    public function getMountedTableBulkActionForm(): ?Form
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableBulkActionForm')) {
            return $this->getCachedForm('mountedTableBulkActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->model($this->getTable()->getModel())
                ->statePath('mountedTableBulkActionData')
                ->operation($this->mountedTableBulkAction),
        );
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<BulkAction>
     */
    protected function getTableBulkActions(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function isTableRecordSelectable(): ?Closure
    {
        return null;
    }
}
