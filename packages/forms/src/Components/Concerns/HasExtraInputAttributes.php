<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraInputAttributes
{
    /**
     * @var array<mixed> | Closure
     */
    protected array | Closure $extraInputAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraInputAttributes(array | Closure $attributes): static
    {
        $this->extraInputAttributes = $attributes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraInputAttributes(): array
    {
        return $this->evaluate($this->extraInputAttributes);
    }

    public function getExtraInputAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraInputAttributes());
    }
}
