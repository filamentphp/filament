<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraModalOverlayAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraModalOverlayAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraModalOverlayAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraModalOverlayAttributes[] = $attributes;
        } else {
            $this->extraModalOverlayAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraModalOverlayAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraModalOverlayAttributes as $extraModalOverlayAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraModalOverlayAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraModalOverlayAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraModalOverlayAttributes());
    }
}
