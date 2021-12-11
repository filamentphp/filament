<?php

namespace Filament\Forms\Components\Concerns;

trait HasMaxItems
{
    protected $maxItems = null;

    public function maxItems(int | callable $count): static
    {
        $this->maxItems = $count;

        $this
            ->rule('array')
            ->rule(function (): string {
                $value = $this->getMaxItems();

                return "max:{$value}";
            });

        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    public function reachedMaxItems(int | callable $count): bool
    {
        return $this->getMaxItems() !== null && $this->evaluate($this->count) < $this->getMaxItems();
    }
}
