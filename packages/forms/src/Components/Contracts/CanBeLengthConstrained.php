<?php

namespace Filament\Forms\Components\Contracts;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanHaveNumericState;
use Filament\Forms\Components\Field;

interface CanBeLengthConstrained
{
    public function getLength(): ?int;

    public function getMaxLength(): ?int;

    public function getMinLength(): ?int;
}
