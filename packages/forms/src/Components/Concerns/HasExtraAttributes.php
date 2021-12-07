<?php

namespace Filament\Forms\Components\Concerns;

trait HasExtraAttributes
{
    protected $extraAttributes = [];

    public function extraAttributes(array | callable $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->evaluate($this->extraAttributes);
    }
}
