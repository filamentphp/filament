<?php

namespace Filament\Forms\Components\Contracts;

use Closure;

interface CanDisableOptions
{
    public function disableOptionWhen(bool | Closure $callback): static;
}
