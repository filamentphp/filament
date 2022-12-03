<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasExtraHeaderAttributes
{
    protected array | Closure $extraHeaderAttributes = [];

    public function extraHeaderAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $attributes = array_merge($this->getExtraHeaderAttributes(), $attributes);
        }

        $this->extraHeaderAttributes = $attributes;

        return $this;
    }

    public function getExtraHeaderAttributes(): array
    {
        return $this->evaluate($this->extraHeaderAttributes);
    }
}
