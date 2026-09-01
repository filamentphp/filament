<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;

class CreateTenantScopedUser extends CreateRecord
{
    protected static string $resource = TenantScopedUserResource::class;
}
