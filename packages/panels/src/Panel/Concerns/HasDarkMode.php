<?php

namespace Filament\Panel\Concerns;

trait HasDarkMode
{
    protected bool $hasDarkMode = true;

    protected bool $hasDarkModeForced = false;

    public function darkMode(bool $condition = true, bool $isForced = false): static
    {
        $this->hasDarkMode = $condition;
        $this->hasDarkModeForced = $isForced;

        return $this;
    }

    public function hasDarkMode(): bool
    {
        return $this->hasDarkMode;
    }

    public function hasDarkModeForced(): bool
    {
        return $this->hasDarkModeForced;
    }
}
