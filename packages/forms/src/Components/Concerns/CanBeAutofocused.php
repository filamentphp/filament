<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAutofocused
{
    protected $isAutofocused = false;

    public function autofocus(bool | callable $condition = true): static
    {
        $this->isAutofocused = $condition;

        return $this;
    }

    public function isAutofocused(): bool
    {
        return (bool) $this->evaluate($this->isAutofocused);
    }
}
