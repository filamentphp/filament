<?php

namespace Filament\Forms\Components\Contracts;

use Closure;

interface CanHideOptions
{
    public function hiddenOptionWhen(bool | Closure $callback): static;
}
