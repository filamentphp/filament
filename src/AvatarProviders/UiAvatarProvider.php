<?php

namespace Filament\AvatarProviders;

use Filament\Models\Contracts\FilamentUser;

class UiAvatarProvider implements Contracts\AvatarProvider
{
    public function get(FilamentUser $user, $size = 48, $dpr = 1)
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF&size='.$size * $dpr;
    }
}
