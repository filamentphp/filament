<?php

namespace Filament\Tests\Fixtures\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class UserManagement extends Cluster
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'User Management';
}
