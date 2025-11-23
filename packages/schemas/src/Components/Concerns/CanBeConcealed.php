<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;

trait CanBeConcealed
{
    protected Component | bool | null $cachedConcealingComponent = null;

    public function getConcealingComponent(): ?Component
    {
        if (filled($this->cachedConcealingComponent)) {
            return $this->cachedConcealingComponent ?: null;
        }

        if (filled($this->getHiddenJs()) || filled($this->getVisibleJs())) {
            return $this->cachedConcealingComponent = $this;
        }

        $parentComponent = $this->getContainer()->getParentComponent();

        if (! $parentComponent) {
            $this->cachedConcealingComponent = false;
        } elseif ($parentComponent instanceof CanConcealComponents && $parentComponent->canConcealComponents()) {
            $this->cachedConcealingComponent = $parentComponent;
        } else {
            $this->cachedConcealingComponent = $parentComponent->getConcealingComponent();
        }

        return $this->cachedConcealingComponent ?: null;
    }

    public function isConcealed(): bool
    {
        return (bool) $this->getConcealingComponent();
    }
}
