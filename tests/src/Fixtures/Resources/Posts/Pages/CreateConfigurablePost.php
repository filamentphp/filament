<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;

class CreateConfigurablePost extends CreateRecord
{
    protected static string $resource = ConfigurablePostResource::class;
}
