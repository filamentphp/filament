<?php

namespace Filament\AvatarProviders;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UiAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Model $user): string
    {
        $name = Str::of(Filament::getUserName($user))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => $segment[0] ?? '')
            ->join('+');

        return 'https://ui-avatars.com/api/?name=' . $name . '&color=FFFFFF&background=111827';
    }
}
