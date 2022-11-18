<?php

namespace Filament\Tables\Columns\Contracts;

use Closure;

interface Editable
{
    public function validate($input): void;

    public function getSaveStateUsing(): ?Closure;
}
