<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraFieldWrapperAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraFieldWrapperAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraFieldWrapperAttributes(array | Closure $attributes, bool $merge = false): static
    {
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

        if ($merge) {
            $this->extraFieldWrapperAttributes[] = $attributes;
        } else {
            $this->extraFieldWrapperAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraFieldWrapperAttributes(): array
    {
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraFieldWrapperAttributes as $extraFieldWrapperAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraFieldWrapperAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraFieldWrapperAttributesBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraFieldWrapperAttributes());
    }
}
