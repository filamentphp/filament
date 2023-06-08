<?php

namespace Filament\Models\Contracts;

use Filament\Panel;

interface FilamentUser
{
    public function canAccessPanel(Panel $panel): bool;
}
