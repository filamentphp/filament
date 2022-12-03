<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraHeaderAttributes
{
    protected array | Closure $extraHeaderAttributes = [];

    public function extraHeaderAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $attributes = $this->getExtraHeaderAttributeBag()->merge($this->evaluate($attributes))->getAttributes();
        }

        $this->extraHeaderAttributes = $attributes;

        return $this;
    }

    public function getExtraHeaderAttributes(): array
    {
        return $this->evaluate($this->extraHeaderAttributes);
    }

    public function getExtraHeaderAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraHeaderAttributes());
    }
}
