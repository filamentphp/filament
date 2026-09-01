<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\NonTenantScopedUsers\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\Tenancy\NonTenantScopedUsers\NonTenantScopedUserResource;

class ListNonTenantScopedUsers extends ListRecords
{
    protected static string $resource = NonTenantScopedUserResource::class;
}
