<?php

namespace Filament\AvatarProviders;

use Filament\Models\Contracts\FilamentUser;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;

class GravatarProvider implements Contracts\AvatarProvider
{
    public function get(FilamentUser $user, $size = 48, $dpr = 1)
    {
        return Gravatar::src($user->email, $size * $dpr);
    }
}
