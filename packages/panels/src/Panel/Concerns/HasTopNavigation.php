<?php

namespace Filament\Panel\Concerns;

trait HasTopNavigation
{
    protected bool $hasTopNavigation = false;

    protected ?string $maxTopNavigationWidth = null;

    public function topNavigation(bool $condition = true): static
    {
        $this->hasTopNavigation = $condition;

        return $this;
    }

    public function hasTopNavigation(): bool
    {
        return $this->hasTopNavigation;
    }

    public function maxTopNavigationWidth(?string $maxTopNavigationWidth): static
    {
        $this->maxTopNavigationWidth = $maxTopNavigationWidth;

        return $this;
    }

    public function getMaxTopNavigationWidth(): ?string
    {
        return $this->maxTopNavigationWidth;
    }
}
