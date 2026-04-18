<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageNotifications extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Notifications';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?int $navigationSort = 2;
}
