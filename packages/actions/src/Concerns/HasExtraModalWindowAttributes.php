<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraModalWindowAttributes
{
    protected array $extraModalWindowAttributes = [];

    public function extraModalWindowAttributes(array | Closure $attributes): static
    {
        $this->extraModalWindowAttributes = $attributes;

        return $this;
    }

    public function getExtraModalWindowAttributes(): array
    {
        return $this->evaluate($this->extraModalWindowAttributes);
    }
}
