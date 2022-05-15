<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait CanBeWizard
{
    protected bool $isWizard = false;

    public function wizard(array | Closure $steps): static
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
