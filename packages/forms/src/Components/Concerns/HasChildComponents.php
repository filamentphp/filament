<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\ComponentContainer;

trait HasChildComponents
{
    protected array $childComponents = [];

    public function childComponents(array $components): static
    {
        $this->childComponents = $components;

        return $this;
    }

    public function schema(array $components): static
    {
        $this->childComponents($components);

        return $this;
    }

    public function getChildComponents(): array
    {
        return $this->childComponents;
    }

    public function getChildComponentContainer(): ?ComponentContainer
    {
        if (! $this->hasChildComponentContainer()) {
            return null;
        }

        return ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->components($this->getChildComponents());
    }

    public function getChildComponentContainers(): array
    {
        if (! $this->hasChildComponentContainer()) {
            return [];
        }

        return [$this->getChildComponentContainer()];
    }

    public function hasChildComponentContainer(): bool
    {
        return ! $this->isHidden() && $this->getChildComponents() !== [];
    }
}
