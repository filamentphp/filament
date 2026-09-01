<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Schemas\Components\Wizard\Step;

trait HasWizard
{
    protected bool $isWizard = false;

    protected bool | Closure $isWizardSkippable = false;

    protected int | Closure $wizardStartStep = 1;

    protected ?Closure $modifyWizardUsing = null;

    /**
     * @param  array<Step> | Closure  $steps
     */
    public function steps(array | Closure $steps): static
    {
        $this->isWizard = true;
        $this->schema($steps);

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

    public function modifyWizardUsing(?Closure $callback): static
    {
        $this->isWizard = true;
        $this->modifyWizardUsing = $callback;

        return $this;
    }

    public function getWizardStartStep(): int
    {
        return $this->evaluate($this->wizardStartStep);
    }
}
