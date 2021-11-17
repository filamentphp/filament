<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanConcealComponents;

trait CanBeConcealed
{
    public function getConcealingComponent(): ?Component
    {
        $parentComponent = $this->getContainer()->getParentComponent();

        if (! $parentComponent) {
            return null;
        }

        if (! $parentComponent instanceof CanConcealComponents) {
            return $parentComponent->getConcealingComponent();
        }

        return $parentComponent;
    }
}
