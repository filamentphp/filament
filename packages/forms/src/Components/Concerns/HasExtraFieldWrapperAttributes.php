<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
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
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraFieldWrapperAttributes as $extraFieldWrapperAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraFieldWrapperAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraFieldWrapperAttributesBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraFieldWrapperAttributes());
    }
}
