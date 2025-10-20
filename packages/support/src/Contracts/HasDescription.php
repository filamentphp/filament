<?php

namespace Filament\Support\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface HasDescription
{
    public function getDescription(): string | Htmlable | null;
}
