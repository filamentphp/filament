<?php

namespace Filament\Tables\Concerns;

trait CanRunActions
{
    public $action;

    public $isActionable = true;

    public $showingActionConfirmationModal = false;

    public $hasConfirmedAction = false;

    public function isActionable()
    {
        return $this->isActionable && count($this->getTable()->getActions());
    }

    public function updatedAction()
    {
        $action = $this->getAction();

        if ($action->getRequiresConfirmation() && $this->showingActionConfirmationModal === false) {
            $this->showingActionConfirmationModal = true;

            return;
        }

        $this->runAction();
    }

    public function updatedShowingActionConfirmationModal($value)
    {
        if ($value === false) $this->reset('action');
    }

    public function updatedHasConfirmedAction()
    {
        $this->runAction();
    }

    protected function runAction()
    {
        $action = $this->getAction();

        if (! $action) {
            return;
        }

        $records = static::getModel() !== null
            ? static::getModel()::find($this->selected)
            : collect($this->selected);

        $action->run($records);

        $this->resetPage();

        $this->action = null;
        $this->showingActionConfirmationModal = false;
        $this->hasConfirmedAction = false;
    }

    public function getAction()
    {
        if (
            ! $this->isActionable() ||
            $this->action === '' ||
            $this->action === null
        ) {
            return;
        }

        return collect($this->getTable()->getActions())
            ->filter(fn ($action) => $action->getName() === $this->action)
            ->first();
    }
}
