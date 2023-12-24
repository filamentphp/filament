<?php

namespace Filament\Panel\Concerns;

trait HasTopBar
{
    protected bool $hasTopBar = false;

    public function topBar(bool $condition = true): static
    {
        $this->hasTopBar = $condition;

        return $this;
    }

    public function hasTopBar(): bool
    {
        return $this->hasTopBar;
    }
}
