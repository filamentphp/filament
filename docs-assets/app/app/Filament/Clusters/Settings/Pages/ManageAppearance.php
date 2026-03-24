<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageAppearance extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Appearance';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?int $navigationSort = 4;
}
