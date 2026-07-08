<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasDarkMode
{
    protected bool | Closure $hasDarkMode = true;

    protected bool | Closure $hasDarkModeForced = false;

    protected bool | Closure $hasDarkModeToggle = true;

    public function darkMode(bool | Closure $condition = true, bool | Closure $isForced = false): static
    {
        $this->hasDarkMode = $condition;
        $this->hasDarkModeForced = $isForced;

        return $this;
    }

    public function darkModeToggle(bool | Closure $condition = true): static
    {
        $this->hasDarkModeToggle = $condition;

        return $this;
    }

    public function hasDarkMode(): bool
    {
        return (bool) $this->evaluate($this->hasDarkMode);
    }

    public function hasDarkModeForced(): bool
    {
        return (bool) $this->evaluate($this->hasDarkModeForced);
    }

    public function hasDarkModeToggle(): bool
    {
        return (bool) $this->evaluate($this->hasDarkModeToggle);
    }
}
