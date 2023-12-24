<?php

namespace Filament\Panel\Concerns;

trait HasTopBar
{
    protected bool $hasTopBar = true;

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
