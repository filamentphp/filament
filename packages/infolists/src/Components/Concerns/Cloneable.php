<?php

namespace Filament\Infolists\Components\Concerns;

use Filament\Infolists\Components\Component;

trait Cloneable
{
    protected function cloneChildComponents(): static
    {
        if (is_array($this->childComponents)) {
            $this->childComponents = array_map(
                fn (Component $component): Component => $component->getClone(),
                $this->childComponents,
            );
        }

        return $this;
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->flushCachedAbsoluteStatePath();
        $clone->cloneChildComponents();

        return $clone;
    }
}
