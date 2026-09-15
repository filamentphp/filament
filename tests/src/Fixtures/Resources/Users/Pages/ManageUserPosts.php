<?php

namespace Filament\Tests\Fixtures\Resources\Users\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Users\Resources\ManagedUserPostResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;

class ManageUserPosts extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'posts';

    protected static ?string $relatedResource = ManagedUserPostResource::class;

    public function table(Table $table): Table
    {
        return ManagedUserPostResource::table($table);
    }
}
