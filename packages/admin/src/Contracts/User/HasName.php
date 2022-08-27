<?php

declare(strict_types=1);

namespace Filament\Contracts\User;

interface HasName
{
    /**
     * Returns the name that is displayed in filament.
     *
     * @return string
     */
    public function getFilamentName(): string;
}
