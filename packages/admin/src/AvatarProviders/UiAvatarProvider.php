<?php

namespace Filament\AvatarProviders;

use Filament\Models\Contracts\FilamentUser;

class UiAvatarProvider implements Contracts\AvatarProvider
{
    public function get(FilamentUser $user): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=FFFFFF&background=111827';
    }
}
