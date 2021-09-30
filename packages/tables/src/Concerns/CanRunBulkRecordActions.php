<?php

namespace Filament\Tables\Concerns;

trait CanRunBulkRecordActions
{
    public $bulkRecordAction;

    public $isBulkActionable = true;

    public $showingBulkActionConfirmationModal = false;

    public $hasConfirmedBulkAction = false;

    public function isBulkActionable()
    {
        return $this->isBulkActionable && count($this->getTable()->getBulkRecordActions());
    }

    public function updatedBulkRecordAction()
    {
        $action = $this->getBulkAction();

        if ($action->getRequiresConfirmation() && $this->showingBulkActionConfirmationModal === false) {
            $this->showingBulkActionConfirmationModal = true;

            return;
        }

        $this->runBulkAction();
    }

    public function updatedShowingBulkActionConfirmationModal($value)
    {
        if ($value === false) $this->reset('bulkRecordAction');
    }

    public function updatedHasConfirmedBulkAction()
    {
        $this->runBulkAction();
    }

    protected function runBulkAction()
    {
        $action = $this->getBulkAction();

        if (! $action) {
            return;
        }

        $records = static::getModel() !== null
            ? static::getModel()::find($this->selected)
            : collect($this->selected);

        $action->run($records);

        $this->resetPage();

        $this->bulkRecordAction = null;
        $this->showingBulkActionConfirmationModal = false;
        $this->hasConfirmedBulkAction = false;
    }

    public function getBulkAction()
    {
        if (
            ! $this->isBulkActionable() ||
            $this->bulkRecordAction === '' ||
            $this->bulkRecordAction === null
        ) {
            return;
        }

        return collect($this->getTable()->getBulkRecordActions())
            ->filter(fn ($action) => $action->getName() === $this->bulkRecordAction)
            ->first();
    }
}
