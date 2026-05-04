<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
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
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

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
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraCellAttributes as $extraCellAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraCellAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraCellAttributeBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraCellAttributes());
    }

    public function hasDynamicExtraCellAttributes(): bool
    {
        foreach ($this->extraCellAttributes as $attributes) {
            if ($attributes instanceof Closure) {
                return true;
            }
        }

        return false;
    }
}
