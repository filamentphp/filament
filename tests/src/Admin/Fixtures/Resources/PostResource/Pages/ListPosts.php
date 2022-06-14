<?php

namespace Filament\Tests\Admin\Fixtures\Resources\PostResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Admin\Fixtures\Resources\PostResource;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;
}
