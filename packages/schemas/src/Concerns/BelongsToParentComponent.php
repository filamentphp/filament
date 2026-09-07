<?php

namespace Filament\Schemas\Concerns;

use Filament\Schemas\Components\Component;

trait BelongsToParentComponent
{
    protected ?Component $parentComponent = null;

    public function parentComponent(Component $component, bool $shouldFlushCachedHierarchy = true): static
    {
        if ($this->parentComponent === $component) {
            return $this;
        }

        $this->parentComponent = $component;

        if ($shouldFlushCachedHierarchy) {
            $this->flushCachedHierarchy();
        }

        return $this;
    }

    public function getParentComponent(): ?Component
    {
        return $this->parentComponent;
    }

    public function isRoot(): bool
    {
        return $this->parentComponent === null;
    }
}
