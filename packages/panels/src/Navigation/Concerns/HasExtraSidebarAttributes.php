<?php

namespace Filament\Navigation\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraSidebarAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraSidebarAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraSidebarAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraSidebarAttributes[] = $attributes;
        } else {
            $this->extraSidebarAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraSidebarAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraSidebarAttributes as $extraSidebarAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraSidebarAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraSidebarAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraSidebarAttributes());
    }
}
