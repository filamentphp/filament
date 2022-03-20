<?php

namespace Filament\Forms\Concerns;

trait CanBeDisabled
{
    protected bool $isDisabled = false;

    public function disabled(bool $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled || $this->getParentComponent()?->isDisabled();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
