<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasWizard
{
    protected bool $isWizard = false;

    public int | Closure $wizardStartStep = 1;

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

    public function isWizard(): bool
    {
        return $this->isWizard;
    }

    public function getWizardStartStep(): int
    {
        return $this->evaluate($this->wizardStartStep);
    }
}
