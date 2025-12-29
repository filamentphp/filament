<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;

class ListTenantScopedUsers extends ListRecords
{
    protected static string $resource = TenantScopedUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
