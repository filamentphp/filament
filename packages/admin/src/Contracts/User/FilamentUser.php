<?php

declare(strict_types=1);

namespace Filament\Contracts\User;

interface FilamentUser
{
    /**
     * Could the user access the filament application?
     *
     * @return bool
     */
    public function canAccessFilament(): bool;
}
