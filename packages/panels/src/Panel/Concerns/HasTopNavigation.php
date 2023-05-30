<?php

namespace Filament\Panel\Concerns;

trait HasTopNavigation
{
    protected bool $hasTopNavigation = false;

    public function topNavigation(bool $condition = true): static
    {
        $this->hasTopNavigation = $condition;

        return $this;
    }

    public function hasTopNavigation(): bool
    {
        return $this->hasTopNavigation;
    }
}
