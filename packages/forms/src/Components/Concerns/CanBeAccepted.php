<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeAccepted
{
    public function accepted(bool | callable $condition = true): static
    {
        $this->addValidationRule('accepted', $condition);

        return $this;
    }
}
