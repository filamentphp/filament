<?php

namespace Filament\Tests\Admin\Fixtures\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Tests\Admin\Fixtures\PostResource;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
}
