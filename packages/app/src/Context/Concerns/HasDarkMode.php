<?php

namespace Filament\Context\Concerns;

trait HasDarkMode
{
    protected bool $hasDarkMode = true;

    public function darkMode(bool $condition = true): static
    {
        $this->hasDarkMode = $condition;

        return $this;
    }

    public function hasDarkMode(): bool
    {
        return $this->hasDarkMode;
    }
}
