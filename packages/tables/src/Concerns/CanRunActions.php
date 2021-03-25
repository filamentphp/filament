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

            $this->dispatchBrowserEvent('open', static::class . 'TableActionsModal');

            return;
        }

        $this->runAction();
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

        $action->run($this->selected);

        $this->resetPage();

        $this->showingActionConfirmationModal = false;

        $this->dispatchBrowserEvent('close', static::class . 'TableActionsModal');
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
