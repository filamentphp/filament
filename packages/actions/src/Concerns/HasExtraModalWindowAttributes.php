<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
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
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

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
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraModalWindowAttributes as $extraModalWindowAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraModalWindowAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraModalWindowAttributeBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraModalWindowAttributes());
    }
}
