<?php

namespace Filament\Forms\Components\Concerns;

trait Cloneable
{
    public function cloneChildComponents(): static
    {
        $components = $this->getChildComponentContainer()->getClone()->getComponents();

        return $this->childComponents($components);
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->cloneChildComponents();

        return $clone;
    }
}
