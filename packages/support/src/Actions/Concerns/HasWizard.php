<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait HasWizard
{
    protected bool $isWizard = false;

    public function steps(array | Closure $steps): static
    {
        $this->isWizard = true;
        $this->form($steps);

        return $this;
    }

    public function isWizard(): bool
    {
        return $this->isWizard;
    }
}
