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

    public function declined(bool | Closure $condition = true): static
    {
        $this->rule('declined', $condition);

        return $this;
    }
}
