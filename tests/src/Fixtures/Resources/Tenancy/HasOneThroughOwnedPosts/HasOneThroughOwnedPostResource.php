<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\HasOneThroughOwnedPosts;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class HasOneThroughOwnedPostResource extends Resource
{
    protected static ?string $model = Post::class;

    // Ownership through a read-only Laravel `HasOneThrough` relationship
    // (post -> author -> team), which cannot `save()` the tenant.
    protected static ?string $tenantOwnershipRelationshipName = 'teamThroughAuthor';

    protected static ?string $slug = 'has-one-through-owned-posts';

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
}
