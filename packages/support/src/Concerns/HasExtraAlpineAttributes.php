<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAlpineAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraAlpineAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraAlpineAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraAlpineAttributes[] = $attributes;
        } else {
            $this->extraAlpineAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraAlpineAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

        foreach ($this->extraAlpineAttributes as $extraAlpineAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraAlpineAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraAlpineAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAlpineAttributes());
    }
}
