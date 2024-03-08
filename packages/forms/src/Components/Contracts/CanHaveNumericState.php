<?php

namespace Filament\Forms\Components\Contracts;

interface CanHaveNumericState
{
    public function isNumeric(): bool;

    public function isInteger(): bool;
}
