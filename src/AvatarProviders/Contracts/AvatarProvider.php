<?php

namespace Filament\AvatarProviders\Contracts;

use Filament\Models\Contracts\FilamentUser;

interface AvatarProvider
{
    public function get(FilamentUser $user);
}
