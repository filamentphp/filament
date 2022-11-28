<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAlpineAttributes
{
    /**
     * @var array<array-key, mixed> | Closure
     */
    protected array | Closure $extraAlpineAttributes = [];

    /**
     * @param array<array-key, mixed> | Closure $attributes
     */
    public function extraAlpineAttributes(array | Closure $attributes): static
    {
        $this->extraAlpineAttributes = array_merge($this->extraAlpineAttributes, $attributes);

        return $this;
    }

    /**
     * @return array<array-key, mixed>
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
