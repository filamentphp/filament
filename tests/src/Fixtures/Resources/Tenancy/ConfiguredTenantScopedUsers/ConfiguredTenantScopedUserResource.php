<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\ConfiguredTenantScopedUsers;

use Filament\Resources\Resource;
use Filament\Resources\ResourceConfiguration;
use Filament\Tests\Fixtures\Models\ConfiguredTenantScopedUser;

class ConfiguredTenantScopedUserResource extends Resource
{
    protected static ?string $model = ConfiguredTenantScopedUser::class;

    protected static ?string $configurationClass = ResourceConfiguration::class;

    protected static ?string $tenantOwnershipRelationshipName = 'teams';
}
