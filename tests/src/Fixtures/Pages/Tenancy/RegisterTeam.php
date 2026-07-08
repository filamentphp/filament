<?php

namespace Filament\Tests\Fixtures\Pages\Tenancy;

use Filament\Pages\Tenancy\RegisterTenant;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }
}
