<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraModalAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraModalAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraModalAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraModalAttributes[] = $attributes;
        } else {
            $this->extraModalAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraModalAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

        foreach ($this->extraModalAttributes as $extraModalAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraModalAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }
}
