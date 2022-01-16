<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasExtraHeaderAttributes
{
    protected array | Closure $extraHeaderAttributes = [];

    public function extraHeaderAttributes(array | Closure $attributes): static
    {
        $this->extraHeaderAttributes = $attributes;

        return $this;
    }

    public function getExtraHeaderAttributes(): array
    {
        return $this->evaluate($this->extraHeaderAttributes);
    }
}
