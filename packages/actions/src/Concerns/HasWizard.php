<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Forms\Components\Wizard\Step;

trait HasWizard
{
    protected bool $isWizard = false;

    protected bool | Closure $isWizardSkippable = false;

    public int | Closure $wizardStartStep = 1;

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
}
