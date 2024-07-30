<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraModalWindowAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraModalWindowAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraModalWindowAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraModalWindowAttributes[] = $attributes;
        } else {
            $this->extraModalWindowAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraModalWindowAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraModalWindowAttributes as $extraModalWindowAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraModalWindowAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraModalWindowAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraModalWindowAttributes());
    }
}
