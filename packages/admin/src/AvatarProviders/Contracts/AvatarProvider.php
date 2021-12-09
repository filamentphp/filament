<?php

namespace Filament\AvatarProviders\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AvatarProvider
{
    public function get(Authenticatable $user): string;
}
