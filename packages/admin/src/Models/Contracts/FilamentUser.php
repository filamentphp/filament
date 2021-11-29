<?php

namespace Filament\Models\Contracts;

interface FilamentUser
{
    public function canAccessFilament(): bool;
}
