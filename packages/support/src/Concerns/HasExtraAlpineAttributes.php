<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAlpineAttributes
{
    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $extraAlpineAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraAlpineAttributes(array | Closure $attributes): static
    {
        $this->extraAlpineAttributes = $attributes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraAlpineAttributes(): array
    {
        return $this->evaluate($this->extraAlpineAttributes);
    }

    public function getExtraAlpineAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAlpineAttributes());
    }
}
