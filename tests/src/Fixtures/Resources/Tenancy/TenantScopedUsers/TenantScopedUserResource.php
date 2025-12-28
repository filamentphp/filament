<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\User;

class TenantScopedUserResource extends Resource
{
    protected static ?string $model = User::class;

    // Use 'teams' (BelongsToMany) relationship for tenant ownership
    // This is needed to test the duplicate pivot insertion bug
    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    protected static ?string $slug = 'tenant-scoped-users';

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
            'index' => Pages\ListTenantScopedUsers::route('/'),
            'create' => Pages\CreateTenantScopedUser::route('/create'),
        ];
    }
}
