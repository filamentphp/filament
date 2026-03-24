<?php

namespace App\Filament\Clusters\Settings;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class SettingsCluster extends Cluster
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $position = request()->query('subNavPosition');

        return match ($position) {
            'end' => SubNavigationPosition::End,
            'top' => SubNavigationPosition::Top,
            default => SubNavigationPosition::Start,
        };
    }
}
