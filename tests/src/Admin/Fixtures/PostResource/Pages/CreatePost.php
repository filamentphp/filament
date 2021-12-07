<?php

namespace Filament\Tests\Admin\Fixtures\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Admin\Fixtures\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
