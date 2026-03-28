<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageWebhooks extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Webhooks';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?int $navigationSort = 9;
}
