<?php

namespace Filament\AvatarProviders;

use Filament\Models\Contracts\FilamentUser;

class UiAvatarProvider implements Contracts\AvatarProvider
{
    public function get(FilamentUser $user, $size = 48, $dpr = 1)
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=FFFFFF&background=0284c7&size='.$size * $dpr;
    }
}
