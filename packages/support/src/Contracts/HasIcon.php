<?php

namespace Filament\Support\Contracts;

use BackedEnum;

interface HasIcon
{
    public function getIcon(): string | BackedEnum | null;
}
