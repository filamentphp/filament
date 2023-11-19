<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Repeater;

trait CanBeRepeated
{
    protected Repeater | bool | null $cachedParentRepeater = null;

    public function getParentRepeater(): ?Repeater
    {
        if (filled($this->cachedParentRepeater)) {
            return $this->cachedParentRepeater ?: null;
        }

        $parentComponent = $this->getContainer()->getParentComponent();

        if (! $parentComponent) {
            $this->cachedParentRepeater = false;
        } elseif ($parentComponent instanceof Repeater) {
            $this->cachedParentRepeater = $parentComponent;
        } else {
            $this->cachedParentRepeater = $parentComponent->getParentRepeater();
        }

        return $this->cachedParentRepeater ?: null;
    }
}
