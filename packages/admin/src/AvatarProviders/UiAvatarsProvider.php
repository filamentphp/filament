<?php

namespace Filament\AvatarProviders;

use Illuminate\Contracts\Auth\Authenticatable;

class UiAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Authenticatable $user): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=FFFFFF&background=111827';
    }
}
