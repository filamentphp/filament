<?php

namespace Filament\Tests\Admin\Fixtures\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Admin\Fixtures\PostResource;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;
}
