<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;

class ListConfigurablePosts extends ListRecords
{
    protected static string $resource = ConfigurablePostResource::class;
}
