<?php

namespace Filament\Infolists\Concerns;

use Exception;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Infolist;

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
