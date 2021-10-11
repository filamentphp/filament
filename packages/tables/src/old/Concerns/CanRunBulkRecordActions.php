<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\BulkAction;

trait CanRunBulkRecordActions
{
    public ?string $bulkRecordAction = null;

    public bool $isBulkActionable = true;

    public bool $showingBulkActionConfirmationModal = false;

    public bool $hasConfirmedBulkAction = false;

    public function updatedBulkRecordAction(): void
    {
        $action = $this->getBulkAction();

        if ($action->getRequiresConfirmation() && $this->showingBulkActionConfirmationModal === false) {
            $this->showingBulkActionConfirmationModal = true;

            return;
        }

        $this->runBulkAction();
    }

    public function getBulkAction(): ?BulkAction
    {
        if (
            ! $this->isBulkActionable() ||
            $this->bulkRecordAction === '' ||
            $this->bulkRecordAction === null
        ) {
            return null;
        }

        return collect($this->getTable()->getBulkRecordActions())
            ->filter(fn ($action) => $action->getName() === $this->bulkRecordAction)
            ->first();
    }

    public function isBulkActionable(): bool
    {
        return $this->isBulkActionable && count($this->getTable()->getBulkRecordActions());
    }

    protected function runBulkAction(): void
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

    public function updatedShowingBulkActionConfirmationModal($value): void
    {
        if ($value !== false) {
            return;
        }

        $this->reset('bulkRecordAction');
    }

    public function updatedHasConfirmedBulkAction(): void
    {
        $this->runBulkAction();
    }
}
