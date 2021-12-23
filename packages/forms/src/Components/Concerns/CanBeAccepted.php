<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeAccepted
{
    public function accepted(bool | Closure $condition = true): static
    {
        $this->rule('accepted', $condition);

        return $this;
    }
}
