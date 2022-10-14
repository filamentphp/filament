<?php

namespace Filament\Tests\App\Fixtures\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\App\Fixtures\Resources\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
