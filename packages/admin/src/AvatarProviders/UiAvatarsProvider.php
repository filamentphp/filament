<?php

namespace Filament\AvatarProviders;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class UiAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Model $user): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode(Filament::getUserName($user)) . '&color=FFFFFF&background=111827';
    }
}
