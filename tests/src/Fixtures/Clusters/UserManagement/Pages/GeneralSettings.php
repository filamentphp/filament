<?php

namespace Filament\Tests\Fixtures\Clusters\UserManagement\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Clusters\UserManagement;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use UnitEnum;

class GeneralSettings extends Page
{
    protected static ?string $cluster = UserManagement::class;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroupEnum::Settings;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'General';

    protected string $view = 'filament-panels::pages.page';
}
