<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraEntryWrapperAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraEntryWrapperAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraEntryWrapperAttributes(array | Closure $attributes, bool $merge = false): static
    {
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

        if ($merge) {
            $this->extraEntryWrapperAttributes[] = $attributes;
        } else {
            $this->extraEntryWrapperAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraEntryWrapperAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraEntryWrapperAttributes as $extraEntryWrapperAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraEntryWrapperAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraEntryWrapperAttributesBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraEntryWrapperAttributes());
    }
}
