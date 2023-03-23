<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
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
        $actions = BulkAction::configureUsing(
            Closure::fromCallable([$this, 'configureTableBulkAction']),
            fn (): array => $this->getTableBulkActions(),
        );

        $this->cachedTableBulkActions = [];

        foreach ($actions as $action) {
            $action->table($this->getCachedTable());

            $this->cachedTableBulkActions[$action->getName()] = $action;
        }
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
    }

    public function callMountedTableBulkAction(?string $arguments = null)
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

        $form = $this->getMountedTableBulkActionForm();

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

        $this->mountedTableBulkAction = null;
        $this->selectedTableRecords = [];

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => "{$this->id}-table-bulk-action",
        ]);

        return $result;
    }

    public function mountTableBulkAction(string $name, array $selectedRecords)
    {
        $this->mountedTableBulkAction = $name;
        $this->selectedTableRecords = $selectedRecords;

        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $this->cacheForm(
            'mountedTableBulkActionForm',
            fn () => $this->getMountedTableBulkActionForm(),
        );

        try {
            if ($action->hasForm()) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableBulkActionForm(),
            ]);

            if ($action->hasForm()) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
            $this->mountedTableBulkAction = null;
            $this->selectedTableRecords = [];

            return;
        }

        if (! $action->shouldOpenModal()) {
            return $this->callMountedTableBulkAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-table-bulk-action",
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

    public function getMountedTableBulkActionForm(): ?ComponentContainer
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableBulkActionForm')) {
            return $this->getCachedForm('mountedTableBulkActionForm');
        }

        return $this->makeForm()
            ->schema($action->getFormSchema())
            ->model($this->getTableQuery()->getModel()::class)
            ->statePath('mountedTableBulkActionData')
            ->context($this->mountedTableBulkAction);
    }

    public function getCachedTableBulkAction(string $name): ?BulkAction
    {
        $action = $this->getCachedTableBulkActions()[$name] ?? null;
        $action?->records($this->getSelectedTableRecords());

        return $action;
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }

    public function isTableRecordSelectable(): ?Closure
    {
        return null;
    }
}
