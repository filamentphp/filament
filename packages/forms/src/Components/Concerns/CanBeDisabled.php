<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;
        $this->dehydrated(fn (Component $component): bool => $component->evaluate($condition));

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->evaluate($this->isDisabled) || $this->getContainer()->isDisabled();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
