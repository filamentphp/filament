<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAttributes
{
    /**
     * @var array<array-key, mixed> | Closure
     */
    protected array | Closure $extraAttributes = [];

    /**
     * @param  array<array-key, mixed> | Closure  $attributes
     */
    public function extraAttributes(array | Closure $attributes): static
    {
        $this->extraAttributes = array_merge($this->extraAttributes, $attributes);

        return $this;
    }

    /**
     * @return array<array-key, mixed>
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
