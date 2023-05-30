<?php

namespace Filament\Models\Contracts;

use Filament\Panel;

interface FilamentUser
{
    public function canAccessFilament(Panel $panel): bool;
}
