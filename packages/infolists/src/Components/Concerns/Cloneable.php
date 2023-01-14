<?php

namespace Filament\Infolists\Components\Concerns;

trait Cloneable
{
    public function getClone(): static
    {
        return clone $this;
    }
}
