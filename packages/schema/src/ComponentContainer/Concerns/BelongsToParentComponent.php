<?php

namespace Filament\Schema\ComponentContainer\Concerns;

use Exception;
use Filament\Infolists\Infolist;
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

    public function getInfolist(): Infolist
    {
        if ($this instanceof Infolist) {
            return $this;
        }

        $parentComponent = $this->getParentComponent();

        if (! $parentComponent) {
            throw new Exception('Tried to access a non-existent root infolist of a component.');
        }

        return $parentComponent->getInfolist();
    }
}
