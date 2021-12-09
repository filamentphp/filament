<?php

namespace Filament\Models\Contracts;

interface HasAvatar
{
    public function getFilamentAvatarUrl(): ?string;
}
