<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

trait CanBeRepeated
{
    protected Schema | bool | null $cachedParentRepeaterItem = null;

    public function getParentRepeater(): Repeater | Builder | null
    {
        $repeater = $this->getParentRepeaterItem()?->getParentComponent();

        assert(($repeater instanceof Repeater) || ($repeater instanceof Builder) || (! $repeater));

        return $repeater;
    }

    public function getParentRepeaterItem(): ?Schema
    {
        if (filled($this->cachedParentRepeaterItem)) {
            return $this->cachedParentRepeaterItem ?: null;
        }

        $container = $this->getContainer();

        $parentComponent = $container->getParentComponent();

        if (! $parentComponent) {
            $this->cachedParentRepeaterItem = false;
        } elseif (($parentComponent instanceof Repeater) || ($parentComponent instanceof Builder)) {
            $this->cachedParentRepeaterItem = $container;
        } else {
            $this->cachedParentRepeaterItem = $parentComponent->getParentRepeaterItem();
        }

        return $this->cachedParentRepeaterItem ?: null;
    }

    public function getParentRepeaterItemIndex(): int
    {
        $item = $this->getParentRepeaterItem();

        if (! $item) {
            return 0;
        }

        $keys = array_keys($item->getParentComponent()->getState());

        $index = array_search(
            $item->getStatePath(isAbsolute: false),
            $keys,
        );

        if ($index === false) {
            return 0;
        }

        return $index;
    }
}
