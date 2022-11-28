<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraHeaderAttributes
{
    /**
     * @var array<array-key, mixed> | Closure
     */
    protected array | Closure $extraHeaderAttributes = [];

    /**
     * @param  array<array-key, mixed> | Closure  $attributes
     */
    public function extraHeaderAttributes(array | Closure $attributes): static
    {
        $this->extraHeaderAttributes = array_merge($this->extraHeaderAttributes, $attributes);

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getExtraHeaderAttributes(): array
    {
        return $this->evaluate($this->extraHeaderAttributes);
    }

    public function getExtraHeaderAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraHeaderAttributes());
    }
}
