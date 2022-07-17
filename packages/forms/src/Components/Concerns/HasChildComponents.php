<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;

trait HasChildComponents
{
    protected array | Closure $childComponents = [];

    public function childComponents(array | Closure $components): static
    {
        $this->childComponents = $components;

        return $this;
    }

    public function schema(array | Closure $components): static
    {
        $this->childComponents($components);

        return $this;
    }

    public function getChildComponents(): array
    {
        return $this->evaluate($this->childComponents);
    }

    public function getChildComponentContainer($key = null): ComponentContainer
    {
        if (filled($key)) {
            return $this->getChildComponentContainers()[$key];
        }

        return ComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->components($this->getChildComponents());
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        if (! $this->hasChildComponentContainer($withHidden)) {
            return [];
        }

        return [$this->getChildComponentContainer()];
    }

    public function hasChildComponentContainer(bool $withHidden = false): bool
    {
        if ((! $withHidden) && $this->isHidden()) {
            return false;
        }

        if ($this->getChildComponents() === []) {
            return false;
        }

        return true;
    }
}
