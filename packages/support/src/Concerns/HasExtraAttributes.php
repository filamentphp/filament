<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraAttributes[] = $attributes;
        } else {
            $this->extraAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraAttributes as $extraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }
}
