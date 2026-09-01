<?php

namespace Filament\Navigation\Concerns;

use Closure;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraTopbarAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraTopbarAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraTopbarAttributes(array | Closure $attributes, bool $merge = false): static
    {
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

        if ($merge) {
            $this->extraTopbarAttributes[] = $attributes;
        } else {
            $this->extraTopbarAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraTopbarAttributes(): array
    {
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraTopbarAttributes as $extraTopbarAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraTopbarAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraTopbarAttributeBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraTopbarAttributes());
    }
}
