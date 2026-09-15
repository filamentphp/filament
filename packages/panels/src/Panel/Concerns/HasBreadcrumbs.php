<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBreadcrumbs
{
    protected bool | Closure $hasBreadcrumbs = true;

    protected bool | Closure $hasStrictHierarchicalBreadcrumbs = false;

    public function breadcrumbs(bool | Closure $condition = true, bool | Closure $strictHierarchical = false): static
    {
        $this->hasBreadcrumbs = $condition;
        $this->hasStrictHierarchicalBreadcrumbs = $strictHierarchical;

        return $this;
    }

    public function hasBreadcrumbs(): bool
    {
        return (bool) $this->evaluate($this->hasBreadcrumbs);
    }

    public function hasStrictHierarchicalBreadcrumbs(): bool
    {
        return (bool) $this->evaluate($this->hasStrictHierarchicalBreadcrumbs);
    }
}
