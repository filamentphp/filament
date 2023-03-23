<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraAttributes
{
    protected array $extraAttributes = [];

    public function extraAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraAttributes[] = $attributes;
        } else {
            $this->extraAttributes = [$attributes];
        }

        return $this;
    }

    public function getExtraAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

        foreach ($this->extraAttributes as $extraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }
}
