<?php

namespace Filament\Infolists\Concerns;

trait CanBeHidden
{
    public function isHidden(): bool
    {
        return (bool) $this->getParentComponent()?->isHidden();
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
