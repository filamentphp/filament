<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeDisabled
{
    protected $isDisabled = false;

    public function disabled(bool | callable $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->evaluate($this->isDisabled) || $this->getContainer()->isDisabled();
    }
}
