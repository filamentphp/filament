<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property Form $mountedTableBulkActionForm
 */
trait HasBulkActions
{
    /**
     * @var array<int | string>
     */
    public array $selectedTableRecords = [];

    protected bool $shouldSelectCurrentPageOnly = false;

    public ?string $mountedTableBulkAction = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $mountedTableBulkActionData = [];

    protected Collection $cachedSelectedTableRecords;

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

    public function deselectAllTableRecords(): void
    {
        $this->emitSelf('deselectAllTableRecords');
    }

    public function getAllTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        $records = $this->getTable()->selectsCurrentPageOnly() ?
            $this->getTableRecords() :
            $query;

        return $records
            ->pluck($query->getModel()->getQualifiedKeyName())
            ->map(fn ($key): string => (string) $key)
            ->all();
    }

    public function getAllTableRecordsCount(): int
    {
        if ($this->getTable()->selectsCurrentPageOnly()) {
            return $this->records->count();
        }

        if ($this->records instanceof LengthAwarePaginator) {
            return $this->records->total();
        }

        $query = $this->getFilteredTableQuery();

        if ($this->getTable()->checksIfRecordIsSelectable()) {
            return $query
                ->get()
                ->filter(fn (Model $record): bool => $this->getTable()->isRecordSelectable($record))
                ->count();
        }

        return $query->count();
    }

    public function getSelectedTableRecords(): Collection
    {
        if (isset($this->cachedSelectedTableRecords)) {
            return $this->cachedSelectedTableRecords;
        }

        $table = $this->getTable();

        if (! ($table->getRelationship() instanceof BelongsToMany && $table->allowsDuplicates())) {
            $query = $table->getQuery()->whereIn(app($table->getModel())->getQualifiedKeyName(), $this->selectedTableRecords);
            $this->applySortingToTableQuery($query);

            return $this->cachedSelectedTableRecords = $query->get();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $table->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $this->cachedSelectedTableRecords = $this->hydratePivotRelationForTableRecords($table->selectPivotDataInQuery(
            $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords),
        )->get());
    }

    public function shouldSelectCurrentPageOnly(): bool
    {
        return $this->shouldSelectCurrentPageOnly;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function shouldDeselectAllRecordsWhenTableFiltered(): bool
    {
        return true;
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
            return $this->getForm('mountedTableBulkActionForm');
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
