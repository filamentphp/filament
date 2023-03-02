<?php

namespace Filament\Forms\Components\Concerns;

trait Cloneable
{
    public function cloneChildComponents(): static
    {
        $components = [];

        foreach ($this->getChildComponents() as $component) {
            $components[] = $component->getClone();
        }

        return $this->childComponents($components);
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->cloneChildComponents();

        return $clone;
    }
}
