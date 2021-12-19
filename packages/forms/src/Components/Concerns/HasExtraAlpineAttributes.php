<?php

namespace Filament\Forms\Components\Concerns;

use Illuminate\View\ComponentAttributeBag;

trait HasExtraAlpineAttributes
{
    protected $extraAlpineAttributes = [];

    public function extraAlpineAttributes(array | callable $attributes): static
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
