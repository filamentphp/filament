<?php

namespace Filament\Infolists\Concerns;

trait Cloneable
{
    public function cloneComponents(): static
    {
        $components = [];

        foreach ($this->getComponents(withHidden: true) as $component) {
            $components[] = $component->getClone();
        }

        return $this->components($components);
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->flushCachedStatePath();
        $clone->cloneComponents();

        return $clone;
    }
}
