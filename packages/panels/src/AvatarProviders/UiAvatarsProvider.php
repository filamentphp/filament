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
            ->map(function (string $segment): string {
                // Skip leading punctuation (e.g. a "[SYSTEM] Admin" service-account
                // naming convention) so it doesn't become part of the initials.
                $letters = preg_replace('/^[^\p{L}\p{N}]+/u', '', $segment);

                return filled($letters) ? mb_substr($letters, 0, 1) : '';
            })
            ->join(' ');

        $background = Color::convertToHex(FilamentColor::getColor('gray')[950] ?? Color::Gray[950]);

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&format=svg&color=FFFFFF&background=' . urlencode($background);
    }
}
