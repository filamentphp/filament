<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraModalWindowAttributes
{
    /**
     * @var array<mixed> | Closure>
     */
    protected array | Closure $extraModalWindowAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraModalWindowAttributes(array | Closure $attributes): static
    {
        $this->extraModalWindowAttributes = $attributes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraModalWindowAttributes(): array
    {
        return $this->evaluate($this->extraModalWindowAttributes);
    }
}
