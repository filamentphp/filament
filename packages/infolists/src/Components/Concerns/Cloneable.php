<?php

namespace Filament\Infolists\Components\Concerns;

trait Cloneable
{
    public function getClone(): static
    {
        $clone = clone $this;
        $clone->flushCachedStatePath();

        return $clone;
    }
}
