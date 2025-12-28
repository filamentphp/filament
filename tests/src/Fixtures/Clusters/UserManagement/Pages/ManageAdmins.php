<?php

namespace Filament\Tests\Fixtures\Clusters\UserManagement\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Clusters\UserManagement;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use UnitEnum;

class ManageAdmins extends Page
{
    protected static ?string $cluster = UserManagement::class;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroupEnum::Users;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Administrators';

    protected string $view = 'filament-panels::pages.page';
}
