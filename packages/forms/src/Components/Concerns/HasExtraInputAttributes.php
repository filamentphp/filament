<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraInputAttributes
{
    protected array | Closure $extraInputAttributes = [];

    public function extraInputAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $attributes = array_merge($this->getExtraInputAttributes(), $attributes);
        }

        $this->extraInputAttributes = $attributes;

        return $this;
    }

    public function getExtraInputAttributes(): array
    {
        return $this->evaluate($this->extraInputAttributes);
    }

    public function getExtraInputAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraInputAttributes());
    }
}
