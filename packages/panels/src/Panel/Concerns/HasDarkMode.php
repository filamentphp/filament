<?php

namespace Filament\Panel\Concerns;

trait HasDarkMode
{
    protected bool $hasDarkMode = true;

    protected bool $hasDarkModeForced = false;

    protected bool $hasLightModeForced = false;

    public function darkMode(bool $condition = true, bool $isForced = false): static
    {
        $this->hasDarkMode = $condition;
        $this->hasDarkModeForced = $isForced;

        return $this;
    }

    public function forceLightMode(bool $condition = true): static
    {
        $this->hasLightModeForced = $condition;

        return $this->darkMode(false, false);
    }

    public function hasDarkMode(): bool
    {
        return $this->hasDarkMode;
    }

    public function hasDarkModeForced(): bool
    {
        return $this->hasDarkModeForced;
    }

    public function hasLightModeForced(): bool
    {
        return $this->hasLightModeForced;
    }
}
