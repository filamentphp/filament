<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasDarkMode
{
    protected bool | Closure $hasDarkMode = true;

    protected bool | Closure $hasDarkModeForced = false;

    protected bool | Closure $hasThemeSwitcher = true;

    public function darkMode(bool | Closure $condition = true, bool | Closure $isForced = false): static
    {
        $this->hasDarkMode = $condition;
        $this->hasDarkModeForced = $isForced;

        return $this;
    }

    public function themeSwitcher(bool | Closure $condition = true): static
    {
        $this->hasThemeSwitcher = $condition;

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

    public function hasThemeSwitcher(): bool
    {
        return (bool) $this->evaluate($this->hasThemeSwitcher);
    }
}
