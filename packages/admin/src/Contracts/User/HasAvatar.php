<?php

declare(strict_types=1);

namespace Filament\Contracts\User;

interface HasAvatar
{
    /**
     * Returns the avatar that is displayed in filament.
     *
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string;
}
