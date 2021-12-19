<?php

namespace Filament\Tables\Columns\Concerns;

trait HasExtraAttributes
{
    protected array $extraAttributes = [];

    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }
}
