<?php

namespace Filament\Context\Concerns;

trait HasBreadcrumbs
{
    protected bool $hasBreadcrumbs = true;

    public function breadcrumbs(bool $condition = true): static
    {
        $this->hasBreadcrumbs = $condition;

        return $this;
    }

    public function hasBreadcrumbs(): bool
    {
        return $this->hasBreadcrumbs;
    }
}
