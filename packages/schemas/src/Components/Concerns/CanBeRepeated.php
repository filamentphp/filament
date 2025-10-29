<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Forms\Components\Repeater;
use Illuminate\Support\Str;

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

    public function getRepeaterItemIndex(): int
    {
        $repeater = $this->getParentRepeater();

        if (! $repeater) {
            return 0;
        }

        // Extract the UUID part from the state path to determine the index.
        $id = Str::of($this->getStatePath())
            ->before(sprintf('.%s', $this->getConstantStatePath()))
            ->afterLast('.')
            ->toString();

        $key = (int) array_search($id, array_keys($repeater->getState()), true);

        return $key;
    }
}
