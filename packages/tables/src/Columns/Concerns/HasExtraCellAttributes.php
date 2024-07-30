<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraCellAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraCellAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraCellAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraCellAttributes[] = $attributes;
        } else {
            $this->extraCellAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraCellAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraCellAttributes as $extraCellAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraCellAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraCellAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraCellAttributes());
    }
}
