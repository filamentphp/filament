<?php

namespace Filament\Schema\Components\Concerns;

use Filament\Schema\ComponentContainer;
use Filament\Schema\Contracts\HasSchemas;
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

    public function getLivewire(): Component & HasSchemas
    {
        return $this->getContainer()->getLivewire();
    }
}
