<?php

namespace Filament\AvatarProviders;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class UiAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Model $user): string
    {
        $name = trim(collect(explode(' ', Filament::getUserName($user)))->map(fn($segment) => $segment[0] ?? '')->join(' '));
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827';
    }
}
