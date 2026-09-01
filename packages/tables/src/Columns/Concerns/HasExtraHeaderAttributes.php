<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraHeaderAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraHeaderAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraHeaderAttributes(array | Closure $attributes, bool $merge = false): static
    {
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

        if ($merge) {
            $this->extraHeaderAttributes[] = $attributes;
        } else {
            $this->extraHeaderAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraHeaderAttributes(): array
    {
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraHeaderAttributes as $extraHeaderAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraHeaderAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraHeaderAttributeBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraHeaderAttributes());
    }
}
