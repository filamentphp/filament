<?php

namespace Filament\Forms\Components\Concerns;

trait HasMinItems
{
    protected $minItems = null;

    public function minItems(int | callable $count): static
    {
        $this->minItems = $count;

        $this->rules(["min:{$this->getMinItems()}"]);

        return $this;
    }

    public function getMinItems(): ?int
    {
        return $this->evaluate($this->minItems);
    }

    public function isGreaterThanMinItems(int | callable $count): bool
    {
        return $this->minItems === null ? true : $this->evaluate($this->count) > $this->getMinItems();
    }
}
