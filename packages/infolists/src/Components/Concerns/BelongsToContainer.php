<?php

namespace Filament\Infolists\Components\Concerns;

use Filament\Infolists\ComponentContainer;
use Filament\Infolists\Contracts\HasInfolists;

trait BelongsToContainer
{
    protected ComponentContainer $container;

    public function container(ComponentContainer $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): ComponentContainer
    {
        return $this->container;
    }
}
