<?php

namespace Filament\AvatarProviders;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;

class UiAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Authenticatable $user): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode(Filament::getUserName($user)) . '&color=FFFFFF&background=111827';
    }
}
