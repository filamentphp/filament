<?php

namespace Filament\Models\Contracts;

use Filament\Context;

interface FilamentUser
{
    public function canAccessFilament(Context $context): bool;
}
