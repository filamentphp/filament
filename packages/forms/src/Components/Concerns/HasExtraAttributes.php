<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasExtraAttributes
{
    protected array | Closure $extraAttributes = [];

    public function extraAttributes(array | Closure $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->evaluate($this->extraAttributes);
    }
}
