<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBreadcrumbs
{
    protected bool | Closure $hasBreadcrumbs = true;

    protected bool | Closure $hasNavigationHierarchyInBreadcrumbs = false;

    public function breadcrumbs(bool | Closure $condition = true, bool | Closure $hasNavigationHierarchy = false): static
    {
        $this->hasBreadcrumbs = $condition;
        $this->hasNavigationHierarchyInBreadcrumbs = $hasNavigationHierarchy;

        return $this;
    }

    public function hasBreadcrumbs(): bool
    {
        return (bool) $this->evaluate($this->hasBreadcrumbs);
    }

    public function hasNavigationHierarchyInBreadcrumbs(): bool
    {
        return (bool) $this->evaluate($this->hasNavigationHierarchyInBreadcrumbs);
    }
}
