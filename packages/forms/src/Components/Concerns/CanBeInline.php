<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeInline
{
    protected $isInline = true;

    public function inline(bool | callable $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
    }
}
