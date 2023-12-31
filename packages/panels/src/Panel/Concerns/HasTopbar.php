<?php

namespace Filament\Panel\Concerns;

trait HasTopbar
{
    protected bool $hasTopbar = true;

    public function topbar(bool $condition = true): static
    {
        $this->hasTopbar = $condition;

        return $this;
    }

    public function hasTopbar(): bool
    {
        return $this->hasTopbar;
    }
}
