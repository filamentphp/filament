<?php

namespace Filament\Forms\Components\Concerns;

trait HasMaxItems
{
    protected $maxItems = null;

    public function maxItems(int | callable $count): static
    {
        $this->maxItems = $count;

        $this->rules(["max:{$this->getMaxItems()}"]);

        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    public function isLessThanMaxItems(int | callable $count): bool
    {
        return $this->maxItems === null ? true : $this->evaluate($this->count) < $this->getMaxItems();
    }
}
