<?php

namespace Filament\Actions\Concerns;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

trait BelongsToSchemaComponent
{
    protected ?Component $schemaComponent = null;

    protected ?Schema $schemaContainer = null;

    public function schemaComponent(?Component $component): static
    {
        $this->schemaComponent = $component;

        return $this;
    }

    public function schemaContainer(?Schema $schema): static
    {
        $this->schemaContainer = $schema;

        return $this;
    }

    public function getSchemaComponent(): ?Component
    {
        return $this->schemaComponent ?? $this->getSchemaContainer()?->getParentComponent() ?? $this->getGroup()?->getSchemaComponent();
    }

    public function getSchemaContainer(): ?Schema
    {
        return $this->schemaContainer ?? $this->getGroup()?->getSchemaContainer();
    }
}
