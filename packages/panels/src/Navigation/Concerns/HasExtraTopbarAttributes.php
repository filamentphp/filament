<?php

namespace Filament\Navigation\Concerns;

use Closure;
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
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraTopbarAttributes as $extraTopbarAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraTopbarAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraTopbarAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraTopbarAttributes());
    }
}
