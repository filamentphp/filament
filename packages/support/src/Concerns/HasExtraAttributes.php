<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
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
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

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
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraAttributes as $extraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraAttributes());
    }

    public function hasExtraAttributes(): bool
    {
        return ! empty($this->extraAttributes);
    }
}
