<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraHeaderAttributes
{
    protected array $extraHeaderAttributes = [];

    public function extraHeaderAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraHeaderAttributes[] = $attributes;
        } else {
            $this->extraHeaderAttributes = [$attributes];
        }

        return $this;
    }

    public function getExtraHeaderAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

        foreach ($this->extraHeaderAttributes as $extraHeaderAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraHeaderAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraHeaderAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraHeaderAttributes());
    }
}
