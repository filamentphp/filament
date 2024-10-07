<?php

namespace Filament\Schema\Concerns;

use Filament\Schema\Components\Component;

trait BelongsToParentComponent
{
    protected ?Component $parentComponent = null;

    public function parentComponent(Component $component): static
    {
        $this->parentComponent = $component;

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
