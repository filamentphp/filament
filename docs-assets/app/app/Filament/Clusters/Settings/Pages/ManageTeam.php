<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageTeam extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Team';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 7;
}
