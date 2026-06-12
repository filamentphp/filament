<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ManageApi extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'API';

    protected static ?string $title = 'API';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?int $navigationSort = 8;
}
