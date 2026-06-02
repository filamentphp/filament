<?php

namespace Filament\AvatarProviders;

use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class UiAvatarsProvider implements Contracts\AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $background = Color::convertToHex(FilamentColor::getColor('gray')[950] ?? Color::Gray[950]);

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&format=svg&color=FFFFFF&background=' . urlencode($background);
    }
}
