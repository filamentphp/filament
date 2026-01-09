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

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function getModelRootContainer(): Schema
    {
        $container = $this->getContainer();
        $model = $this->getModel();
        $record = $this->getRecord();

        while (($parentComponent = $container->getParentComponent()) !== null) {
            if (
                ($parentComponent->getModel() !== $model) ||
                ($parentComponent->getRecord() !== $record)
            ) {
                break;
            }

            $container = $parentComponent->getContainer();
        }

        return $container;
    }

    public function getLivewire(): Component & HasSchemas
    {
        return $this->getContainer()->getLivewire();
    }
}
