<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
