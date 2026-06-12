<?php

namespace Filament\Support\Contracts;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

interface HasIcon
{
    public function getIcon(): string | BackedEnum | Htmlable | null;
}
