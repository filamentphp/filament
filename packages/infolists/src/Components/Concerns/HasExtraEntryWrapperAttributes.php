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
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraEntryWrapperAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraEntryWrapperAttributesBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraEntryWrapperAttributes());
    }
}
