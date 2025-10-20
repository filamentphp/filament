<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

trait BelongsToContainer
{
    protected Schema $container;

    protected Schema $rootContainer;

    public function container(Schema $schema): static
    {
        $this->container = $schema;

        return $this;
    }

    public function getContainer(): Schema
    {
        return $this->container;
    }

    public function getRootContainer(): Schema
    {
        return $this->rootContainer ??= (function (): Schema {
            $container = $this->getContainer();

            while (($parentComponent = $container->getParentComponent()) !== null) {
                $container = $parentComponent->getContainer();
            }

            return $container;
        })();
    }

    public function getLivewire(): Component & HasSchemas
    {
        return $this->getContainer()->getLivewire();
    }
}
