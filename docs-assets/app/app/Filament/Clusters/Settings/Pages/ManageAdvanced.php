<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageAdvanced extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedWrench;

    protected static ?int $navigationSort = 10;
}
