<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Infolists\ComponentContainer;
use Illuminate\Database\Eloquent\Model;

class RepeatableEntry extends Entry
{
    use Concerns\HasContainerGridLayout;

    protected bool | Closure $isWrappedInCard = true;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.repeatable-entry';

    /**
     * @return array<ComponentContainer>
     */
    public function getChildComponentContainers(bool $withHidden = false): array
    {
        $containers = [];

        foreach ($this->getState() ?? [] as $itemKey => $itemData) {
            $container = $this
                ->getChildComponentContainer()
                ->getClone()
                ->statePath($itemKey)
                ->inlineLabel(false);

            if ($itemData instanceof Model) {
                $container->record($itemData);
            }

            $containers[$itemKey] = $container;
        }

        return $containers;
    }

    public function wrappedInCard(bool | Closure $condition = true): static
    {
        $this->isWrappedInCard = $condition;

        return $this;
    }

    public function isWrappedInCard(): bool
    {
        return (bool) $this->evaluate($this->isWrappedInCard);
    }
}
