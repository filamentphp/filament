<?php

namespace Filament\Forms\Components\Contracts;

use Closure;

interface CanHideOptions
{
    public function hideOptionWhen(bool | Closure $callback): static;
}
