<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBreadcrumbs
{
    protected bool | Closure $hasBreadcrumbs = true;

    public function breadcrumbs(bool | Closure $condition = true): static
    {
        $this->hasBreadcrumbs = $condition;

        return $this;
    }

    public function hasBreadcrumbs(): bool
    {
        return (bool) $this->evaluate($this->hasBreadcrumbs);
    }
}
