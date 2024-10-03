<?php

namespace Filament\Infolists\Components;

use Filament\Infolists\ComponentContainer;
use Filament\Support\Concerns\CanBeContained;
use Illuminate\Database\Eloquent\Model;

class RepeatableEntry extends Entry
{
    use CanBeContained;
    use Concerns\HasContainerGridLayout;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.repeatable-entry';

    /**
     * @return array<ComponentContainer>
     */
    public function getChildComponentContainers(bool $withHidden = false): array
    {
        if ((! $withHidden) && $this->isHidden()) {
            return [];
        }

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
}
