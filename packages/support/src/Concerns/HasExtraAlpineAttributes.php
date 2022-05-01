<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAlpineAttributes
{
    protected array | Closure $extraAlpineAttributes = [];

    public function extraAlpineAttributes(array | Closure $attributes): static
    {
        $this->extraAlpineAttributes = $attributes;

        return $this;
    }

    public function getExtraAlpineAttributes(): array
    {
        return $this->evaluate($this->extraAlpineAttributes);
    }

    public function getExtraAlpineAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAlpineAttributes());
    }
}
