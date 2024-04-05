<?php

namespace Filament\Schema\Components\Concerns;

use Filament\Schema\Contracts\HasSchemas;
use Filament\Schema\Schema;
use Livewire\Component;

trait BelongsToContainer
{
    protected Schema $container;

    public function container(Schema $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): Schema
    {
        return $this->container;
    }

    public function getLivewire(): Component & HasSchemas
    {
        return $this->getContainer()->getLivewire();
    }
}
