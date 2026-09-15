<?php

namespace Filament\Tests\Fixtures\Resources\Tenancy\HasOneThroughOwnedPosts;

use Filament\Resources\Resource;
use Filament\Tests\Fixtures\Models\Post;

class HasOneThroughOwnedPostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $tenantOwnershipRelationshipName = 'teamThroughAuthor';
}
