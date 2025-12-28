<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\NonTenantScopedUsers;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\User;

class NonTenantScopedUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'non-tenant-scoped-users';

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNonTenantScopedUsers::route('/'),
        ];
    }
}
