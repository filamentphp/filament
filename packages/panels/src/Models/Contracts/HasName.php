<?php

namespace Filament\Models\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface HasName
{
    public function getFilamentName(): string | Htmlable;
}
