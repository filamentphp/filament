<?php

namespace Filament\Forms\Components\Concerns;

trait HasPlaceholder
{
    protected $placeholder = null;

    public function placeholder(string | callable $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->evaluate($this->placeholder);
    }
}
