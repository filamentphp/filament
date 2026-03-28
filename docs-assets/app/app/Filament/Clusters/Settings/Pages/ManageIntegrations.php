<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageIntegrations extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Integrations';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?int $navigationSort = 3;
}
