<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Wizard\Step;
use Filament\Support\Enums\IconPosition;

trait HasWizard
{
    protected bool $isWizard = false;

    protected bool | Closure $isWizardSkippable = false;

    protected int | Closure $wizardStartStep = 1;

    protected ?Closure $modifyNextActionUsing = null;

    protected ?Closure $modifyPreviousActionUsing = null;

    /**
     * @param  array<Step> | Closure  $steps
     */
    public function steps(array | Closure $steps): static
    {
        $this->isWizard = true;
        $this->form($steps);

        return $this;
    }

    public function startOnStep(int | Closure $startStep): static
    {
        $this->wizardStartStep = $startStep;

        return $this;
    }

    public function skippableSteps(bool | Closure $condition = true): static
    {
        $this->isWizardSkippable = $condition;

        return $this;
    }

    public function isWizard(): bool
    {
        return $this->isWizard;
    }

    public function isWizardSkippable(): bool
    {
        return (bool) $this->evaluate($this->isWizardSkippable);
    }

    public function getWizardStartStep(): int
    {
        return $this->evaluate($this->wizardStartStep);
    }

    public function getWizardNextAction(): Action
    {
        $action = Action::make($this->getNextActionName())
            ->label(__('filament-forms::components.wizard.actions.next_step.label'))
            ->iconPosition(IconPosition::After)
            ->livewireClickHandlerEnabled(false)
            ->button();

        if ($this->modifyNextActionUsing) {
            $action = $this->evaluate($this->modifyNextActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function nextAction($callback): static
    {
        $this->modifyNextActionUsing = $callback;

        return $this;
    }

    public function getNextActionName(): string
    {
        return 'next';
    }

        public function getWizardPreviousAction(): Action
    {
        $action = Action::make($this->getPreviousActionName())
            ->label(__('filament-forms::components.wizard.actions.previous_step.label'))
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->button();

        if ($this->modifyPreviousActionUsing) {
            $action = $this->evaluate($this->modifyPreviousActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function previousAction(?Closure $callback): static
    {
        $this->modifyPreviousActionUsing = $callback;

        return $this;
    }

    public function getPreviousActionName(): string
    {
        return 'previous';
    }
}
