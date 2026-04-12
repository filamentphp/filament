<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraInputAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraInputAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraInputAttributes(array | Closure $attributes, bool $merge = false): static
    {
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

        if ($merge) {
            $this->extraInputAttributes[] = $attributes;
        } else {
            $this->extraInputAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraInputAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraInputAttributes as $extraInputAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraInputAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraInputAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraInputAttributes());
    }
}
