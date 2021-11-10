<?php

namespace Filament\Models\Contracts;

interface HasAvatar
{
    public function getFilamentAvatar(): ?string;
}
