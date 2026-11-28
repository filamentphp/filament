<?php

namespace Filament\Tests\Fixtures\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tests\Fixtures\Resources\Posts\ReorderablePostResource;

class ReorderableRelatedPostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    protected static ?string $relatedResource = ReorderablePostResource::class;
}
