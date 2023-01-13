<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAttributes
{
    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $extraAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraAttributes(array | Closure $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraAttributes(): array
    {
        return $this->evaluate($this->extraAttributes);
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }
}
