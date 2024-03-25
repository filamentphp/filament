<?php

namespace Filament\Infolists\Components\Concerns;

use Filament\Infolists\Infolist;
use Filament\Schema\ComponentContainer;
use Livewire\Component;

trait BelongsToContainer
{
    protected ComponentContainer $container;

    public function container(ComponentContainer $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): ComponentContainer
    {
        return $this->container;
    }

    public function getInfolist(): Infolist
    {
        return $this->getContainer()->getInfolist();
    }

    public function getLivewire(): ?Component
    {
        return $this->getContainer()->getLivewire();
    }
}
