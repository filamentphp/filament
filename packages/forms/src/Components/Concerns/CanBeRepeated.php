<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;

trait CanBeRepeated
{
    protected Component | bool | null $cachedRepeaterComponent = null;

    public function getRepeaterComponent(): ?Component
    {
        if (filled($this->cachedRepeaterComponent)) {
            return $this->cachedRepeaterComponent ?: null;
        }

        $parentComponent = $this->getContainer()->getParentComponent();

        if (! $parentComponent) {
            $this->cachedRepeaterComponent = false;
        } elseif ($parentComponent instanceof Repeater) {
            $this->cachedRepeaterComponent = $parentComponent;
        } else {
            $this->cachedRepeaterComponent = $parentComponent->getRepeaterComponent();
        }

        return $this->cachedRepeaterComponent ?: null;
    }

    public function isRepeated(): bool
    {
        return (bool) $this->getRepeaterComponent();
    }
}
