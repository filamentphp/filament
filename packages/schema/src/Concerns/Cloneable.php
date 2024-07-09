<?php

namespace Filament\Schema\Concerns;

use Filament\Schema\Components\Component;

trait Cloneable
{
    protected function cloneComponents(): static
    {
        if (is_array($this->components)) {
            $this->components = array_map(
                fn (Component $component): Component => $component
                    ->container($this)
                    ->getClone(),
                $this->components,
            );
        }

        return $this;
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->flushCachedAbsoluteKey();
        $clone->flushCachedAbsoluteStatePath();
        $clone->flushCachedInheritanceKey();
        $clone->cloneComponents();

        return $clone;
    }
}
