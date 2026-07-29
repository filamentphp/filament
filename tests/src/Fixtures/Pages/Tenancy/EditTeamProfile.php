<?php

namespace Filament\Tests\Fixtures\Pages\Tenancy;

use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Team profile';
    }
}
